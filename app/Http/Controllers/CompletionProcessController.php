<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompletionProcessController extends Controller
{ 
    protected $table = 'completion_process';

    // List all records with pagination
    public function index()
    {
        $query = DB::table($this->table)
            ->leftJoin('employees', $this->table.'.employee_id', '=', 'employees.id')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', $this->table.'.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', $this->table.'.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', $this->table.'.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('completion_process_types', $this->table.'.completion_process_type_id', '=', 'completion_process_types.id');

        if (session('role_id') == 2) {
            $query->where($this->table.'.zila_id', session('zila_id'))
                  ->where($this->table.'.tehsil_id', session('tehsil_id'));
        }

        $completion_process = $query->select(
                $this->table.'.*',
                'employees.nam as employee_name',
                'employee_type.ahalkar_title as employee_type_title',
                'districts.districtNameUrdu as districtNameUrdu',
                'tehsils.tehsilNameUrdu as tehsilNameUrdu',
                'mozas.mozaNameUrdu as mozaNameUrdu',
                'completion_process_types.title_ur as completion_process_type_title',
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 1 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS mizan_khata_dar_khatoni"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 2 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS pukhta_khatoni_drandkas_khasra"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 3 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS durusti_badrat"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 4 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS tehreer_naqal_shajra_nasab"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 5 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS tehreer_shajra_nasab_malkan_qabza"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 6 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS pukhta_khatajat"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 7 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS kham_khatajat_dar_shajra_nasab"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 8 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS tehreer_mushtarka_khata"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 9 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS pukhta_numberwan_dar_khatoni"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 10 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS kham_numberwan_dar_khatoni"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 11 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS tasdeeq_akhir"),
                DB::raw("IF(\n                    ".$this->table.".completion_process_type_id = 12 AND COALESCE(TRIM(".$this->table.".type_value), '') <> '', ".$this->table.".type_value, '-'\n                ) AS mutafarriq_kaam")
            )
            ->orderBy($this->table.'.id', 'desc')
            ->paginate(10);
        return view('completion_process.index', compact('completion_process'));
    }

    // Show form to create a new record
    public function create()
    {
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = DB::table('districts')->orderBy('districtId')->get();
        $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
    } else {
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
        $mozas     = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
    }
    $employees = DB::table('employees')->orderBy('nam')->get();
    $completion_process_types = DB::table('completion_process_types')->orderBy('id')->get();
    return view('completion_process.create', compact('districts', 'tehsils', 'mozas', 'employees', 'completion_process_types', 'role_id'));
    }

    // Store a new record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'completion_process_type_id' => 'required|integer',
            'type_value' => 'nullable|string',
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'tareekh' => 'nullable|date',
            'tabsara' => 'nullable|string',
            'operator_id' => 'nullable|integer',
        ]);

        DB::table($this->table)->insert($validated);

        return redirect()->route('completion_process.index')->with('success', 'Record added successfully.');
    }

    // Show form to edit a record
    public function edit($id)
    {
    $completion_process = DB::table($this->table)
        ->leftJoin('districts', $this->table.'.zila_id', '=', 'districts.districtId')
        ->leftJoin('tehsils', $this->table.'.tehsil_id', '=', 'tehsils.tehsilId')
        ->leftJoin('mozas', $this->table.'.moza_id', '=', 'mozas.mozaId')
        ->select($this->table.'.*', 'districts.districtNameUrdu as district_name', 'tehsils.tehsilNameUrdu as tehsil_name', 'mozas.mozaNameUrdu as moza_name')
        ->where($this->table.'.id', $id)
        ->first();
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = DB::table('districts')->orderBy('districtId')->get();
        $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
    } else {
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
        $mozas     = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
    }
    $employees = DB::table('employees')->orderBy('nam')->get();
    $completion_process_types = DB::table('completion_process_types')->orderBy('id')->get();
    return view('completion_process.edit', compact('completion_process', 'districts', 'tehsils', 'mozas', 'employees', 'completion_process_types', 'role_id'));
    }

    // Update a record
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'completion_process_type_id' => 'required|integer',
            'type_value' => 'nullable|string',
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'tareekh' => 'nullable|date',
            'tabsara' => 'nullable|string',
            'operator_id' => 'nullable|integer',
        ]);

        DB::table($this->table)->where('id', $id)->update($validated);

        return redirect()->route('completion_process.index')
                         ->with('success', 'Record updated successfully.');
    }

    // Delete a record
    public function destroy($id)
    {
        DB::table($this->table)->where('id', $id)->delete();
        return redirect()->route('completion_process.index')
                         ->with('success', 'Record deleted successfully.');
    }

    // Get target values for a moza and completion process type
    public function getTargetValue(Request $request)
    {
        $moza_id = $request->moza_id;
        $completion_process_type_id = $request->completion_process_type_id;

        $target = DB::table('completion_process_target_values_to_achieve')
            ->leftJoin('mozas', 'completion_process_target_values_to_achieve.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('tehsils', 'mozas.tehsilId', '=', 'tehsils.tehsilId')
            ->where('completion_process_target_values_to_achieve.moza_id', $moza_id)
            ->where('completion_process_target_values_to_achieve.completion_process_type_id', $completion_process_type_id)
            ->select('completion_process_target_values_to_achieve.*', 'mozas.tehsilId as tehsil_id', 'tehsils.districtId as district_id')
            ->first();

        return response()->json($target);
    }

    // Store or update target value
    public function storeTargetValue(Request $request)
    {
        if ($request->has('target_value_id') && $request->target_value_id) {
            // Update existing record
            $request->validate([
                'target_value_id' => 'required|integer',
                'target_value' => 'required|numeric',
            ]);

            DB::table('completion_process_target_values_to_achieve')
                ->where('target_value_id', $request->target_value_id)
                ->update(['target_value' => $request->target_value]);

            return response()->json(['success' => true, 'message' => 'Target value updated successfully.']);exit;
        } else {
            // Create new record
            $validated = $request->validate([
                'moza_id' => 'required|integer',
                'completion_process_type_id' => 'required|integer',
                'target_value' => 'required|numeric',
            ]);

            $existing = DB::table('completion_process_target_values_to_achieve')
                ->where('moza_id', $request->moza_id)
                ->where('completion_process_type_id', $request->completion_process_type_id)
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Target value already exists for this location and type.']);exit;
            }

            DB::table('completion_process_target_values_to_achieve')->insert($validated);
            return response()->json(['success' => true, 'message' => 'Target value added successfully.']);
        }
    }

    // Get all target values for display
    public function getTargetValues()
    {
        $query = DB::table('completion_process_target_values_to_achieve')
            ->leftJoin('mozas', 'completion_process_target_values_to_achieve.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('completion_process_types', 'completion_process_target_values_to_achieve.completion_process_type_id', '=', 'completion_process_types.id')
            ->select(
                'completion_process_target_values_to_achieve.*',
                'mozas.mozaNameUrdu as moza_name',
                'completion_process_types.title_ur as type_title'
            );

        if (session('role_id') == 2) {
            $query->where('mozas.tehsilId', session('tehsil_id'));
        }

        $target_values = $query->get();

        return response()->json($target_values);
    }

    // DataTables server-side processing
    public function datatable(Request $request)
    {
        $columns = [
            'completion_process.id',
            'districts.districtNameUrdu',
            'tehsils.tehsilNameUrdu',
            'mozas.mozaNameUrdu',
            'employees.nam',
            'employee_type.ahalkar_title',
            'completion_process.tareekh'
        ];

        $query = DB::table($this->table)
            ->leftJoin('employees', $this->table.'.employee_id', '=', 'employees.id')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', $this->table.'.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', $this->table.'.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', $this->table.'.moza_id', '=', 'mozas.mozaId')
            ->leftJoin('completion_process_types', $this->table.'.completion_process_type_id', '=', 'completion_process_types.id');

        // Role-based filtering
        if (session('role_id') == 2) {
            $query->where($this->table.'.zila_id', session('zila_id'))
                  ->where($this->table.'.tehsil_id', session('tehsil_id'));
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('completion_process.id', 'like', '%' . $search . '%')
                  ->orWhere('districts.districtNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('tehsils.tehsilNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('mozas.mozaNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('employees.nam', 'like', '%' . $search . '%')
                  ->orWhere('employee_type.ahalkar_title', 'like', '%' . $search . '%');
            });
        }

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection);
            }
        } else {
            $query->orderBy('completion_process.id', 'desc');
        }

        // Pagination
        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'completion_process.*',
            'employees.nam as employee_name',
            'employee_type.ahalkar_title as employee_type_title',
            'districts.districtNameUrdu as districtNameUrdu',
            'tehsils.tehsilNameUrdu as tehsilNameUrdu',
            'mozas.mozaNameUrdu as mozaNameUrdu',
            'completion_process_types.title_ur as completion_process_type_title'
        )->get();

        // Group records by id to aggregate the type values
        $groupedRecords = [];
        foreach ($records as $record) {
            $id = $record->id;
            if (!isset($groupedRecords[$id])) {
                $groupedRecords[$id] = [
                    'id' => $record->id,
                    'districtNameUrdu' => $record->districtNameUrdu,
                    'tehsilNameUrdu' => $record->tehsilNameUrdu,
                    'mozaNameUrdu' => $record->mozaNameUrdu,
                    'employee_name' => $record->employee_name,
                    'employee_type_title' => $record->employee_type_title,
                    'tareekh' => $record->tareekh ? date('d-m-Y', strtotime($record->tareekh)) : '',
                    'mizan_khata_dar_khatoni' => '',
                    'pukhta_khatoni_drandkas_khasra' => '',
                    'durusti_badrat' => '',
                    'tehreer_naqal_shajra_nasab' => '',
                    'tehreer_shajra_nasab_malkan_qabza' => '',
                    'pukhta_khatajat' => '',
                    'kham_khatajat_dar_shajra_nasab' => '',
                    'tehreer_mushtarka_khata' => '',
                    'pukhta_numberwan_dar_khatoni' => '',
                    'kham_numberwan_dar_khatoni' => '',
                    'tasdeeq_akhir' => '',
                    'mutafarriq_kaam' => '',
                    'actions' => '<a href="' . route('completion_process.edit', $record->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> ترمیم</a>'
                ];
            }

            // Set the appropriate field based on completion_process_type_id
            switch ($record->completion_process_type_id) {
                case 1:
                    $groupedRecords[$id]['mizan_khata_dar_khatoni'] = $record->type_value ?: '-';
                    break;
                case 2:
                    $groupedRecords[$id]['pukhta_khatoni_drandkas_khasra'] = $record->type_value ?: '-';
                    break;
                case 3:
                    $groupedRecords[$id]['durusti_badrat'] = $record->type_value ?: '-';
                    break;
                case 4:
                    $groupedRecords[$id]['tehreer_naqal_shajra_nasab'] = $record->type_value ?: '-';
                    break;
                case 5:
                    $groupedRecords[$id]['tehreer_shajra_nasab_malkan_qabza'] = $record->type_value ?: '-';
                    break;
                case 6:
                    $groupedRecords[$id]['pukhta_khatajat'] = $record->type_value ?: '-';
                    break;
                case 7:
                    $groupedRecords[$id]['kham_khatajat_dar_shajra_nasab'] = $record->type_value ?: '-';
                    break;
                case 8:
                    $groupedRecords[$id]['tehreer_mushtarka_khata'] = $record->type_value ?: '-';
                    break;
                case 9:
                    $groupedRecords[$id]['pukhta_numberwan_dar_khatoni'] = $record->type_value ?: '-';
                    break;
                case 10:
                    $groupedRecords[$id]['kham_numberwan_dar_khatoni'] = $record->type_value ?: '-';
                    break;
                case 11:
                    $groupedRecords[$id]['tasdeeq_akhir'] = $record->type_value ?: '-';
                    break;
                case 12:
                    $groupedRecords[$id]['mutafarriq_kaam'] = $record->type_value ?: '-';
                    break;
            }
        }

        $data = array_values($groupedRecords);

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }
}
