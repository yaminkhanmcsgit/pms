<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use PDF;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $district = 'مالاکنڈ ڈویژن';
        $report_date = date('Y-m-d');

        // Fetch all districts and tehsils for dropdowns
        $districts = DB::table('districts')->get();
        $tehsils = DB::table('tehsils')->leftJoin('districts', 'tehsils.tehsilId', '=', 'districts.districtId')->select('tehsils.*', 'districts.districtNameUrdu as district_name')->get();

        // Get selected filter values
        $selected_district = $request->input('district_id');
        $selected_tehsil = $request->input('tehsil_id');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // For role 2, force filters to their assigned district/tehsil if not set
        if (session('role_id') == 2) {
            if (!$selected_district) {
                $selected_district = session('zila_id');
            }
            if (!$selected_tehsil) {
                $selected_tehsil = session('tehsil_id');
            }
        }

        // Default to last 7 days if no dates provided
        if (!$from_date || !$to_date) {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime('-6 days'));
        }

        // Grouped partal records by district, tehsil, moza, employee
      $partal_query = DB::table('partal')
   ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
   ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
   ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId')
   ->when($selected_district, function($q) use ($selected_district) { $q->where('districts.districtId', $selected_district); })
   ->when($selected_tehsil, function($q) use ($selected_tehsil) { $q->where('tehsils.tehsilId', $selected_tehsil); })
   ->when(Schema::hasColumn('partal', 'tareekh_partal'), function($q) use ($from_date, $to_date) {
       $q->whereBetween('partal.tareekh_partal', [$from_date, $to_date]);
   })
    ->groupBy(
        'districts.districtId',
        'districts.districtNameUrdu',   // added
        'tehsils.tehsilId',
        'tehsils.tehsilNameUrdu',       // added
        'mozas.mozaId',
        'mozas.mozaNameUrdu',           // added
        'partal.patwari_nam',
        'partal.ahalkar_nam'
    )
    ->select(
        'districts.districtId',
        'tehsils.tehsilId',
        'mozas.mozaId',
        'districts.districtNameUrdu as districtNameUrdu',
        'tehsils.tehsilNameUrdu as tehsilNameUrdu',
        'mozas.mozaNameUrdu as mozaNameUrdu',
        'partal.patwari_nam',
        'partal.ahalkar_nam',
        DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_pemuda_khasra,0)) AS tasdeeq_milkiat_pemuda_khasra'),
        DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_pemuda_khasra_badrat,0)) AS tasdeeq_milkiat_pemuda_khasra_badrat'),
        DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_qabza_kasht_khasra,0)) AS tasdeeq_milkiat_qabza_kasht_khasra'),
        DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_qabza_kasht_badrat,0)) AS tasdeeq_milkiat_qabza_kasht_badrat'),
        DB::raw('SUM(COALESCE(partal.tasdeeq_shajra_nasab_guri,0)) AS tasdeeq_shajra_nasab_guri'),
        DB::raw('SUM(COALESCE(partal.tasdeeq_shajra_nasab_badrat,0)) AS tasdeeq_shajra_nasab_badrat'),
        DB::raw('SUM(COALESCE(partal.muqabala_khatoni_chomanda,0)) AS muqabala_khatoni_chomanda'),
        DB::raw('SUM(COALESCE(partal.muqabala_khatoni_chomanda_badrat,0)) AS muqabala_khatoni_chomanda_badrat'),
        DB::raw('GROUP_CONCAT(DISTINCT partal.tabsara SEPARATOR "; ") AS tabsara')
    )
    ->orderBy('districts.districtId')
    ->orderBy('tehsils.tehsilId')
    ->orderBy('mozas.mozaId')
    ->get();

        $records = $partal_query;

        // Grouped completion_process records by district, tehsil, moza, employee, type
        $cp_query = DB::table('completion_process')
            ->leftJoin('employees', 'completion_process.employee_id', '=', 'employees.id')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', 'completion_process.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'completion_process.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'completion_process.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('completion_process_types', 'completion_process.completion_process_type_id', '=', 'completion_process_types.id')
            ->when($selected_district, function($q) use ($selected_district) { $q->where('completion_process.zila_id', $selected_district); })
            ->when($selected_tehsil, function($q) use ($selected_tehsil) { $q->where('completion_process.tehsil_id', $selected_tehsil); })
            ->when(Schema::hasColumn('completion_process', 'tareekh'), function($q) use ($from_date, $to_date) { $q->whereBetween('completion_process.tareekh', [$from_date, $to_date]); })
            ->groupBy(
                'completion_process.zila_id',
                'districts.districtNameUrdu',
                'completion_process.tehsil_id',
                'tehsils.tehsilNameUrdu',
                'completion_process.moza_id',
                'mozas.mozaNameUrdu',
                'completion_process.employee_id',
                'employees.nam',
                'employee_type.ahalkar_title',
                'completion_process.completion_process_type_id',
                'completion_process_types.title_ur'
            )
            ->select(
                'completion_process.zila_id', 'districts.districtNameUrdu as districtNameUrdu',
                'completion_process.tehsil_id', 'tehsils.tehsilNameUrdu as tehsilNameUrdu',
                'completion_process.moza_id', 'mozas.mozaNameUrdu as mozaNameUrdu',
                'completion_process.employee_id', 'employees.nam as employee_name',
                'employee_type.ahalkar_title as employee_type_title',
                'completion_process.completion_process_type_id', 'completion_process_types.title_ur as completion_process_type_title',
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 1 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mizan_khata_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 2 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_khatoni_drandkas_khasra"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 3 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS durusti_badrat"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 4 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_naqal_shajra_nasab"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 5 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_shajra_nasab_malkan_qabza"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 6 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_khatajat"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 7 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS kham_khatajat_dar_shajra_nasab"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 8 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_mushtarka_khata"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 9 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_numberwan_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 10 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS kham_numberwan_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 11 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tasdeeq_akhir"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 12 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mutafarriq_kaam"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 1) AS target_mizan_khata_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 2) AS target_pukhta_khatoni_drandkas_khasra"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 3) AS target_durusti_badrat"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 4) AS target_tehreer_naqal_shajra_nasab"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 5) AS target_tehreer_shajra_nasab_malkan_qabza"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 6) AS target_pukhta_khatajat"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 7) AS target_kham_khatajat_dar_shajra_nasab"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 8) AS target_tehreer_mushtarka_khata"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 9) AS target_pukhta_numberwan_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 10) AS target_kham_numberwan_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 11) AS target_tasdeeq_akhir"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 12) AS target_mutafarriq_kaam")
            )
            ->orderBy('completion_process.zila_id')
            ->orderBy('completion_process.tehsil_id')
            ->orderBy('completion_process.moza_id')
            ->orderBy('completion_process.employee_id')
            ->get();
        $completion_process = $cp_query;

        // Grievances from last 7 days
        $grievances_query = DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId')
            ->when($selected_district, function($q) use ($selected_district) { $q->where('districts.districtId', $selected_district); })
            ->when($selected_tehsil, function($q) use ($selected_tehsil) { $q->where('tehsils.tehsilId', $selected_tehsil); })
            ->whereBetween('grievances.application_date', [$from_date, $to_date])
            ->select(
                'grievances.*',
                'grievance_types.name as grievance_type_name',
                'grievance_statuses.name as status_name',
                'grievance_statuses.color_class as status_color',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->orderBy('grievances.application_date', 'desc')
            ->get();

        $grievances = $grievances_query;

        // Chart data
        $current_year = date('Y');

        // Grievances by type for different periods
        $grievances_by_type = [
            'current_year' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereYear('grievances.created_at', $current_year)
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
            'last_year' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereYear('grievances.created_at', $current_year - 1)
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
            'current_month' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereYear('grievances.created_at', $current_year)
                ->whereMonth('grievances.created_at', date('m'))
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
            'last_month' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereYear('grievances.created_at', date('m') == 1 ? $current_year - 1 : $current_year)
                ->whereMonth('grievances.created_at', date('m') == 1 ? 12 : date('m') - 1)
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
            'current_week' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereBetween('grievances.created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
            'last_week' => DB::table('grievances')
                ->join('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
                ->whereBetween('grievances.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
                ->select('grievance_types.name as type_name', DB::raw('COUNT(grievances.id) as count'))
                ->groupBy('grievance_types.id', 'grievance_types.name')
                ->having('count', '>', 0)
                ->pluck('count', 'type_name')
                ->toArray(),
        ];
        $chart_data = [
            'grievances' => [
                'current_year' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereYear('grievances.created_at', $current_year)
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
                'last_year' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereYear('grievances.created_at', $current_year - 1)
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
                'current_month' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereYear('grievances.created_at', $current_year)
                    ->whereMonth('grievances.created_at', date('m'))
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
                'last_month' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereYear('grievances.created_at', date('m') == 1 ? $current_year - 1 : $current_year)
                    ->whereMonth('grievances.created_at', date('m') == 1 ? 12 : date('m') - 1)
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
                'current_week' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereBetween('grievances.created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
                'last_week' => $this->addTotalToData(DB::table('grievances')
                    ->join('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
                    ->select('grievance_statuses.name as status_name', DB::raw('COUNT(*) as count'))
                    ->whereBetween('grievances.created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
                    ->groupBy('grievance_statuses.name')
                    ->pluck('count', 'status_name')->toArray()),
            ],
            'completion_process' => [
                'current_year' => ['Total' => DB::table('completion_process')->whereYear('created_at', $current_year)->count()],
                'last_year' => ['Total' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->count()],
                'current_month' => ['Total' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->count()],
                'last_month' => ['Total' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->count()],
                'current_week' => ['Total' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()],
                'last_week' => ['Total' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count()],
            ],
            'partal' => [
                'current_year' => ['Total' => DB::table('partal')->whereYear('created_at', $current_year)->count()],
                'last_year' => ['Total' => DB::table('partal')->whereYear('created_at', $current_year - 1)->count()],
                'current_month' => ['Total' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->count()],
                'last_month' => ['Total' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->count()],
                'current_week' => ['Total' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()],
                'last_week' => ['Total' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count()],
            ],
        ];

        $role_id = session('role_id');
        $assigned_district_name = '';
        $assigned_tehsil_name = '';
        if ($role_id == 2) {
            $assigned_district_name = DB::table('districts')->where('districtId', session('zila_id'))->value('districtNameUrdu');
            $assigned_tehsil_name = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->value('tehsilNameUrdu');
        }
        // Partal summary sums for different periods
        $partal_sums = [
            'current_year' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereYear('created_at', $current_year)->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereYear('created_at', $current_year)->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereYear('created_at', $current_year)->sum('muqabala_khatoni_chomanda_badrat'),
            ],
            'last_year' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereYear('created_at', $current_year - 1)->sum('muqabala_khatoni_chomanda_badrat'),
            ],
            'current_month' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('muqabala_khatoni_chomanda_badrat'),
            ],
            'last_month' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('muqabala_khatoni_chomanda_badrat'),
            ],
            'current_week' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('muqabala_khatoni_chomanda_badrat'),
            ],
            'last_week' => [
                'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_milkiat_pemuda_khasra'),
                'تعداد برامدہ بدرات (پیمود)' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_milkiat_pemuda_khasra_badrat'),
                'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_milkiat_qabza_kasht_khasra'),
                'تعداد برامدہ بدرات (قبضہ)' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_milkiat_qabza_kasht_badrat'),
                'تعداد گھری' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_shajra_nasab_guri'),
                'تعداد برامدہ بدرات (شجرہ)' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_shajra_nasab_badrat'),
                'مقابلہ کھتونی ہمراہ کاپی چومنڈہ' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('muqabala_khatoni_chomanda'),
                'تعداد برامدہ بدرات (مقابلہ)' => DB::table('partal')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('muqabala_khatoni_chomanda_badrat'),
            ],
        ];

        // Completion process summary sums for different periods
        $completion_process_sums = [
            'current_year' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereYear('created_at', $current_year)->sum('mutafarriq_kaam'),
            ],
            'last_year' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereYear('created_at', $current_year - 1)->sum('mutafarriq_kaam'),
            ],
            'current_month' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereYear('created_at', $current_year)->whereMonth('created_at', date('m'))->sum('mutafarriq_kaam'),
            ],
            'last_month' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereYear('created_at', date('m') == 1 ? $current_year - 1 : $current_year)->whereMonth('created_at', date('m') == 1 ? 12 : date('m') - 1)->sum('mutafarriq_kaam'),
            ],
            'current_week' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('mutafarriq_kaam'),
            ],
            'last_week' => [
                'میزان کھاتہ دار/کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('mizan_khata_dar_khatoni'),
                'پختہ کھتونی درانڈکس خسرہ' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('pukhta_khatoni_drandkas_khasra'),
                'درستی بدرات' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('durusti_badrat'),
                'تحریر نقل شجرہ نسب' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tehreer_naqal_shajra_nasab'),
                'تحریر شجرہ نسب مالکان قبضہ' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tehreer_shajra_nasab_malkan_qabza'),
                'پختہ کھاتاجات' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('pukhta_khatajat'),
                'خام کھاتہ جات در شجرہ نسب' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('kham_khatajat_dar_shajra_nasab'),
                'تحریر مشترکہ کھاتہ' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tehreer_mushtarka_khata'),
                'پختہ نمبرواں در کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('pukhta_numberwan_dar_khatoni'),
                'خام نمبرواں در کھتونی' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('kham_numberwan_dar_khatoni'),
                'تصدیق آخیر' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('tasdeeq_akhir'),
                'متفرق کام' => DB::table('completion_process')->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('mutafarriq_kaam'),
            ],
        ];

        return view('dashboard', compact('district', 'report_date', 'records', 'completion_process', 'grievances', 'chart_data', 'districts', 'tehsils', 'selected_district', 'selected_tehsil', 'from_date', 'to_date', 'role_id', 'assigned_district_name', 'assigned_tehsil_name', 'partal_sums', 'completion_process_sums', 'grievances_by_type'));
    }

    private function addTotalToData($data)
    {
        $data['Total'] = array_sum($data);
        return $data;
    }

    public function reports()
    {
        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils = DB::table('tehsils')->orderBy('tehsilId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $tehsils = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
        }
        $mozas = DB::table('mozas')->orderBy('mozaId')->get();

        // Default to last 7 days
        $from_date = date('Y-m-d', strtotime('-6 days'));
        $to_date = date('Y-m-d');

        // Get default data
        $partal_data = $this->getPartalReportsData($role_id == 2 ? session('zila_id') : null, $role_id == 2 ? session('tehsil_id') : null, null, $from_date, $to_date);
        $completion_data = $this->getCompletionProcessReportsData($role_id == 2 ? session('zila_id') : null, $role_id == 2 ? session('tehsil_id') : null, null, null, $from_date, $to_date);
        $grievances_data = $this->getGrievancesReportsData($role_id == 2 ? session('zila_id') : null, $role_id == 2 ? session('tehsil_id') : null, null, $from_date, $to_date);

        $zila_name = null;
        $tehsil_name = null;
        if (session('role_id') == 2) {
            $zila_name = DB::table('districts')->where('districtId', session('zila_id'))->value('districtNameUrdu');
            $tehsil_name = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->value('tehsilNameUrdu');
        }

        return view('reports.index', compact('districts', 'tehsils', 'mozas', 'role_id', 'from_date', 'to_date', 'partal_data', 'completion_data', 'grievances_data', 'zila_name', 'tehsil_name'));
    }

    private function getPartalReportsData($district, $tehsil, $moza, $from_date, $to_date)
    {
        return DB::table('partal')
            ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId')
            ->when($district, function($q) use ($district) { $q->where('districts.districtId', $district); })
            ->when($tehsil, function($q) use ($tehsil) { $q->where('tehsils.tehsilId', $tehsil); })
            ->when($moza, function($q) use ($moza) { $q->where('mozas.mozaId', $moza); })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                $q->whereBetween('partal.tareekh_partal', [$from_date, $to_date]);
            })
            ->groupBy('districts.districtId', 'districts.districtNameUrdu', 'tehsils.tehsilId', 'tehsils.tehsilNameUrdu', 'mozas.mozaId', 'mozas.mozaNameUrdu', 'partal.patwari_nam', 'partal.ahalkar_nam')
            ->select(
                'districts.districtId',
                'districts.districtNameUrdu',
                'tehsils.tehsilId',
                'tehsils.tehsilNameUrdu',
                'mozas.mozaId',
                'mozas.mozaNameUrdu',
                'partal.patwari_nam',
                'partal.ahalkar_nam',
                DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_pemuda_khasra,0)) AS tasdeeq_milkiat_pemuda_khasra'),
                DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_pemuda_khasra_badrat,0)) AS tasdeeq_milkiat_pemuda_khasra_badrat'),
                DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_qabza_kasht_khasra,0)) AS tasdeeq_milkiat_qabza_kasht_khasra'),
                DB::raw('SUM(COALESCE(partal.tasdeeq_milkiat_qabza_kasht_badrat,0)) AS tasdeeq_milkiat_qabza_kasht_badrat'),
                DB::raw('SUM(COALESCE(partal.tasdeeq_shajra_nasab_guri,0)) AS tasdeeq_shajra_nasab_guri'),
                DB::raw('SUM(COALESCE(partal.tasdeeq_shajra_nasab_badrat,0)) AS tasdeeq_shajra_nasab_badrat'),
                DB::raw('SUM(COALESCE(partal.muqabala_khatoni_chomanda,0)) AS muqabala_khatoni_chomanda'),
                DB::raw('SUM(COALESCE(partal.muqabala_khatoni_chomanda_badrat,0)) AS muqabala_khatoni_chomanda_badrat'),
                DB::raw('GROUP_CONCAT(DISTINCT partal.tabsara SEPARATOR "; ") AS tabsara')
            )
            ->orderBy('districts.districtId')
            ->orderBy('tehsils.tehsilId')
            ->orderBy('mozas.mozaId')
            ->get();
    }

    private function getCompletionProcessReportsData($district, $tehsil, $moza, $employee, $from_date, $to_date)
    {
        return DB::table('completion_process')
            ->leftJoin('employees', 'completion_process.employee_id', '=', 'employees.id')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', 'completion_process.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'completion_process.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'completion_process.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('completion_process_types', 'completion_process.completion_process_type_id', '=', 'completion_process_types.id')
            ->when($district, function($q) use ($district) { $q->where('completion_process.zila_id', $district); })
            ->when($tehsil, function($q) use ($tehsil) { $q->where('completion_process.tehsil_id', $tehsil); })
            ->when($moza, function($q) use ($moza) { $q->where('completion_process.moza_id', $moza); })
            ->when($employee, function($q) use ($employee) { $q->where('completion_process.employee_id', $employee); })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                $q->whereBetween('completion_process.tareekh', [$from_date, $to_date]);
            })
            ->groupBy('completion_process.zila_id', 'districts.districtNameUrdu', 'completion_process.tehsil_id', 'tehsils.tehsilNameUrdu', 'completion_process.moza_id', 'mozas.mozaNameUrdu', 'completion_process.employee_id', 'employees.nam', 'employee_type.ahalkar_title')
            ->select(
                'completion_process.zila_id', 'districts.districtNameUrdu as districtNameUrdu',
                'completion_process.tehsil_id', 'tehsils.tehsilNameUrdu as tehsilNameUrdu',
                'completion_process.moza_id', 'mozas.mozaNameUrdu as mozaNameUrdu',
                'completion_process.employee_id', 'employees.nam as employee_name',
                'employee_type.ahalkar_title as employee_type_title',
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 1 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mizan_khata_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 2 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_khatoni_drandkas_khasra"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 3 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS durusti_badrat"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 4 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_naqal_shajra_nasab"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 5 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_shajra_nasab_malkan_qabza"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 6 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_khatajat"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 7 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS kham_khatajat_dar_shajra_nasab"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 8 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tehreer_mushtarka_khata"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 9 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS pukhta_numberwan_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 10 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS kham_numberwan_dar_khatoni"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 11 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS tasdeeq_akhir"),
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 12 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mutafarriq_kaam"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 1) AS target_mizan_khata_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 2) AS target_pukhta_khatoni_drandkas_khasra"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 3) AS target_durusti_badrat"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 4) AS target_tehreer_naqal_shajra_nasab"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 5) AS target_tehreer_shajra_nasab_malkan_qabza"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 6) AS target_pukhta_khatajat"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 7) AS target_kham_khatajat_dar_shajra_nasab"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 8) AS target_tehreer_mushtarka_khata"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 9) AS target_pukhta_numberwan_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 10) AS target_kham_numberwan_dar_khatoni"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 11) AS target_tasdeeq_akhir"),
                DB::raw("(SELECT COALESCE(target_value, 0) FROM completion_process_target_values_to_achieve WHERE moza_id = completion_process.moza_id AND completion_process_type_id = 12) AS target_mutafarriq_kaam")
            )
            ->orderBy('completion_process.zila_id')
            ->orderBy('completion_process.tehsil_id')
            ->orderBy('completion_process.moza_id')
            ->orderBy('completion_process.employee_id')
            ->orderBy('completion_process.completion_process_type_id')
            ->get();
    }

    private function getGrievancesReportsData($district, $tehsil, $moza, $from_date, $to_date)
    {
        return DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId')
            ->when($district, function($q) use ($district) { $q->where('districts.districtId', $district); })
            ->when($tehsil, function($q) use ($tehsil) { $q->where('tehsils.tehsilId', $tehsil); })
            ->when($moza, function($q) use ($moza) { $q->where('mozas.mozaId', $moza); })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                $q->whereBetween('grievances.application_date', [$from_date, $to_date]);
            })
            ->select(
                'grievances.*',
                'grievance_types.name as grievance_type_name',
                'grievance_statuses.name as status_name',
                'grievance_statuses.color_class as status_color',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->orderBy('grievances.application_date', 'desc')
            ->get();
    }

    public function getPartalReports(Request $request)
    {
        $selected_district = $request->input('district_id') !== 'undefined' ? $request->input('district_id') : null;
        $selected_tehsil = $request->input('tehsil_id') !== 'undefined' ? $request->input('tehsil_id') : null;
        $selected_moza = $request->input('moza_id') !== 'undefined' ? $request->input('moza_id') : null;
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // For role 2, force filters
        if (session('role_id') == 2) {
            if (!$selected_district) {
                $selected_district = session('zila_id');
            }
            if (!$selected_tehsil) {
                $selected_tehsil = session('tehsil_id');
            }
        }

        $query = DB::table('partal')
            ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId')
            ->when($selected_district, function($q) use ($selected_district) { $q->where('districts.districtId', $selected_district); })
            ->when($selected_tehsil, function($q) use ($selected_tehsil) { $q->where('tehsils.tehsilId', $selected_tehsil); })
            ->when($selected_moza, function($q) use ($selected_moza) { $q->where('mozas.mozaId', $selected_moza); })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                $q->whereBetween('partal.tareekh_partal', [$from_date, $to_date]);
            })
            ->select(
                'partal.*',
                'districts.districtNameUrdu',
                'tehsils.tehsilNameUrdu',
                'mozas.mozaNameUrdu'
            )
            ->orderBy('partal.tareekh_partal', 'desc')
            ->get();

        if ($request->has('pdf')) {
            return PDF::loadView('reports.partal_pdf', compact('query', 'from_date', 'to_date'))->download('partal_report.pdf');
        }

        if ($request->has('excel')) {
            $filename = 'partal_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            $callback = function() use ($query, $from_date, $to_date) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['سیریل نمبر', 'ضلع نام', 'تحصیل نام', 'موضع نام', 'پٹوار نام', 'اہلکار نام', 'از تاریخ', 'تا تاریخ', 'تصدیق ملکیت/پیمود شدہ نمبرات خسرہ', 'تعداد برامدہ بدرات', 'تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ', 'تعداد برامدہ بدرات', 'تعداد گھری', 'تعداد برامدہ بدرات', 'مقابلہ کھتونی ہمراہ کاپی چومنڈہ', 'تعداد برامدہ بدرات', 'تبصرہ']);
                foreach ($query as $index => $row) {
                    fputcsv($file, [$index + 1, $row->districtNameUrdu, $row->tehsilNameUrdu, $row->mozaNameUrdu, $row->patwari_nam, $row->ahalkar_nam, $from_date, $to_date, $row->tasdeeq_milkiat_pemuda_khasra, $row->tasdeeq_milkiat_pemuda_khasra_badrat, $row->tasdeeq_milkiat_qabza_kasht_khasra, $row->tasdeeq_milkiat_qabza_kasht_badrat, $row->tasdeeq_shajra_nasab_guri, $row->tasdeeq_shajra_nasab_badrat, $row->muqabala_khatoni_chomanda, $row->muqabala_khatoni_chomanda_badrat, $row->tabsara]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return response()->json($query);
    }

    public function getCompletionProcessReports(Request $request)
    {
        $selected_district = $request->input('district_id') !== 'undefined' ? $request->input('district_id') : null;
        $selected_tehsil = $request->input('tehsil_id') !== 'undefined' ? $request->input('tehsil_id') : null;
        $selected_moza = $request->input('moza_id') !== 'undefined' ? $request->input('moza_id') : null;
        $selected_employee = $request->input('employee_id') !== 'undefined' ? $request->input('employee_id') : null;
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // For role 2, force filters
        if (session('role_id') == 2) {
            if (!$selected_district) {
                $selected_district = session('zila_id');
            }
            if (!$selected_tehsil) {
                $selected_tehsil = session('tehsil_id');
            }
        }

        $query = $this->getCompletionProcessReportsData($selected_district, $selected_tehsil, $selected_moza, $selected_employee, $from_date, $to_date);

        if ($request->has('pdf')) {
            return PDF::loadView('reports.completion_pdf', compact('query', 'from_date', 'to_date'))->download('completion_report.pdf');
        }

        if ($request->has('excel')) {
            $filename = 'completion_process_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            $callback = function() use ($query, $from_date, $to_date) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['نمبر شمار', 'نام ضلع', 'نام تحصیل', 'نام موضع', 'نام اہلکار', 'میزان کھاتہ دار/کھتونی', 'پختہ کھتونی درانڈکس خسرہ', 'درستی بدرات', 'تحریر نقل شجرہ نسب', 'تحریر شجرہ نسب مالکان قبضہ', 'پختہ کھاتاجات', 'خام کھاتہ جات در شجرہ نسب', 'تحریر مشترکہ کھاتہ', 'پختہ نمبرواں در کھتونی', 'خام نمبرواں در کھتونی', 'تصدیق آخیر', 'متفرق کام', 'از تاریخ', 'تا تاریخ']);
                foreach ($query as $index => $row) {
                    fputcsv($file, [$index + 1, $row->districtNameUrdu, $row->tehsilNameUrdu, $row->mozaNameUrdu, $row->employee_name, $row->mizan_khata_dar_khatoni, $row->pukhta_khatoni_drandkas_khasra, $row->durusti_badrat, $row->tehreer_naqal_shajra_nasab, $row->tehreer_shajra_nasab_malkan_qabza, $row->pukhta_khatajat, $row->kham_khatajat_dar_shajra_nasab, $row->tehreer_mushtarka_khata, $row->pukhta_numberwan_dar_khatoni, $row->kham_numberwan_dar_khatoni, $row->tasdeeq_akhir, $row->mutafarriq_kaam, $from_date, $to_date]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return response()->json($query);
    }

    public function getGrievancesReports(Request $request)
    {
        $selected_district = $request->input('district_id') !== 'undefined' ? $request->input('district_id') : null;
        $selected_tehsil = $request->input('tehsil_id') !== 'undefined' ? $request->input('tehsil_id') : null;
        $selected_moza = $request->input('moza_id') !== 'undefined' ? $request->input('moza_id') : null;
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // For role 2, force filters
        if (session('role_id') == 2) {
            if (!$selected_district) {
                $selected_district = session('zila_id');
            }
            if (!$selected_tehsil) {
                $selected_tehsil = session('tehsil_id');
            }
        }

        $query = DB::table('grievances')
            ->leftJoin('grievance_types', 'grievances.grievance_type_id', '=', 'grievance_types.id')
            ->leftJoin('grievance_statuses', 'grievances.status_id', '=', 'grievance_statuses.id')
            ->leftJoin('districts', 'grievances.district', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'grievances.tehsil', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'grievances.village_name', '=', 'mozas.mozaId')
            ->when($selected_district, function($q) use ($selected_district) { $q->where('districts.districtId', $selected_district); })
            ->when($selected_tehsil, function($q) use ($selected_tehsil) { $q->where('tehsils.tehsilId', $selected_tehsil); })
            ->when($selected_moza, function($q) use ($selected_moza) { $q->where('mozas.mozaId', $selected_moza); })
            ->when($from_date && $to_date, function($q) use ($from_date, $to_date) {
                $q->whereBetween('grievances.application_date', [$from_date, $to_date]);
            })
            ->select(
                'grievances.*',
                'grievance_types.name as grievance_type_name',
                'grievance_statuses.name as status_name',
                'grievance_statuses.color_class as status_color',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->orderBy('grievances.application_date', 'desc')
            ->get();

        if ($request->has('pdf')) {
            return PDF::loadView('reports.grievances_pdf', compact('query', 'from_date', 'to_date'))->download('grievances_report.pdf');
        }

        if ($request->has('excel')) {
            $filename = 'grievances_report_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            $callback = function() use ($query, $from_date, $to_date) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['نمبر شمار', 'نام ضلع', 'نام تحصیل', 'نام موضع', 'شکایت کنندہ کا نام', 'والد کا نام', 'شناختی کارڈ نمبر', 'شکایت کی قسم', 'حیثیت', 'تاریخ']);
                foreach ($query as $index => $row) {
                    fputcsv($file, [$index + 1, $row->district_name, $row->tehsil_name, $row->moza_name, $row->applicant_name, $row->father_name, $row->cnic, $row->grievance_type_name, $row->status_name, $row->application_date ? date('d-m-Y', strtotime($row->application_date)) : '']);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return response()->json($query);
    }
}

