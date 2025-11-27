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
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 12 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mutafarriq_kaam")
            )
            ->orderBy('completion_process.zila_id')
            ->orderBy('completion_process.tehsil_id')
            ->orderBy('completion_process.moza_id')
            ->orderBy('completion_process.employee_id')
            ->get();
        $completion_process = $cp_query;

       

        $role_id = session('role_id');
        $assigned_district_name = '';
        $assigned_tehsil_name = '';
        if ($role_id == 2) {
            $assigned_district_name = DB::table('districts')->where('districtId', session('zila_id'))->value('districtNameUrdu');
            $assigned_tehsil_name = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->value('tehsilNameUrdu');
        }
        return view('dashboard', compact('district', 'report_date', 'records', 'completion_process', 'districts', 'tehsils', 'selected_district', 'selected_tehsil', 'from_date', 'to_date', 'role_id', 'assigned_district_name', 'assigned_tehsil_name'));
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
        $completion_data = $this->getCompletionProcessReportsData($role_id == 2 ? session('zila_id') : null, $role_id == 2 ? session('tehsil_id') : null, null, $from_date, $to_date);
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

    private function getCompletionProcessReportsData($district, $tehsil, $moza, $from_date, $to_date)
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
                DB::raw("SUM(CASE WHEN completion_process.completion_process_type_id = 12 AND COALESCE(TRIM(completion_process.type_value), '') <> '' AND completion_process.type_value REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$' THEN CAST(completion_process.type_value AS UNSIGNED) ELSE 0 END) AS mutafarriq_kaam")
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

        $query = $this->getCompletionProcessReportsData($selected_district, $selected_tehsil, $selected_moza, $from_date, $to_date);

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
