<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartalController extends Controller
{
    protected $table = 'partal';

    // List all records with pagination
    public function index()
    {
        $query = DB::table($this->table)
            ->leftJoin('employees as emp_ahalkar', 'partal.ahalkar_nam', '=', 'emp_ahalkar.nam')
            ->leftJoin('employee_type as et_ahalkar', 'emp_ahalkar.ahalkar_type', '=', 'et_ahalkar.ahalkar_type_id')
            ->leftJoin('employees as emp_patwari', 'partal.patwari_nam', '=', 'emp_patwari.nam')
            ->leftJoin('employee_type as et_patwari', 'emp_patwari.ahalkar_type', '=', 'et_patwari.ahalkar_type_id')
            ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId');

        if (session('role_id') == 2) {
            $query->where('districts.districtId', session('zila_id'))
                  ->where('tehsils.tehsilId', session('tehsil_id'));
        }

        $records = $query->select(
                'partal.*',
                'et_ahalkar.ahalkar_title as ahalkar_title',
                'et_patwari.ahalkar_title as patwari_title',
                'districts.districtNameUrdu as districtNameUrdu',
                'tehsils.tehsilNameUrdu as tehsilNameUrdu',
                'mozas.mozaNameUrdu as mozaNameUrdu',
                'districts.districtId as zila_id',
                'tehsils.tehsilId as tehsil_id',
                'mozas.mozaId as moza_id'
            )
            ->orderBy('partal.id', 'desc')
            ->paginate(10);
        return view('partal.index', compact('records'));
    }

    // Show form to create a new record
    public function create()
    {
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = DB::table('districts')->orderBy('districtId')->get();
        $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
        $mozas = DB::table('mozas')->orderBy('mozaId')->get();
    } else {
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
        $mozas = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
    }
    $employees = DB::table('employees')->orderBy('nam')->get();
    return view('partal.create', compact('districts', 'tehsils', 'mozas', 'employees', 'role_id'));
    }

    // Store a new record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'patwari_nam' => 'nullable|string|max:100',
            'ahalkar_nam' => 'nullable|string|max:100',
            'tareekh_partal' => 'nullable|date',
            'tasdeeq_milkiat_pemuda_khasra' => 'nullable|integer',
            'tasdeeq_milkiat_pemuda_khasra_badrat' => 'nullable|integer',
            'tasdeeq_milkiat_qabza_kasht_khasra' => 'nullable|integer',
            'tasdeeq_milkiat_qabza_kasht_badrat' => 'nullable|integer',
            'tasdeeq_shajra_nasab_guri' => 'nullable|integer',
            'tasdeeq_shajra_nasab_badrat' => 'nullable|integer',
            'muqabala_khatoni_chomanda' => 'nullable|integer',
            'muqabala_khatoni_chomanda_badrat' => 'nullable|integer',
            'tabsara' => 'nullable|string',
            'operator_id' => 'nullable|integer',
        ]);

        // Keep IDs as they are
        $validated['zila_nam'] = $validated['zila_id'];
        $validated['tehsil_nam'] = $validated['tehsil_id'];
        $validated['moza_nam'] = $validated['moza_id'];
        unset($validated['zila_id'], $validated['tehsil_id'], $validated['moza_id']);

        $validated['operator_id'] = session('operator_id');

        DB::table($this->table)->insert($validated);

        return redirect()->route('partal.index')
                         ->with('success', 'Record added successfully.');
    }

    // Show form to edit a record
    public function edit($id)
    {
        $record = DB::table($this->table)
            ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId')
            ->select(
                'partal.*',
                'districts.districtId as zila_id',
                'tehsils.tehsilId as tehsil_id',
                'mozas.mozaId as moza_id'
            )
            ->where('partal.id', $id)
            ->first();
        $role_id = session('role_id');
        if ($role_id == 1) {
            $districts = DB::table('districts')->orderBy('districtId')->get();
            $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
            $mozas = DB::table('mozas')->orderBy('mozaId')->get();
        } else {
            $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
            $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
            $mozas = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
        }
        $employees = DB::table('employees')->orderBy('nam')->get();
        return view('partal.edit', compact('record', 'districts', 'tehsils', 'mozas', 'employees', 'role_id'));
    }

    // Update a record
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'patwari_nam' => 'nullable|string|max:100',
            'ahalkar_nam' => 'nullable|string|max:100',
            'tareekh_partal' => 'nullable|date',
            'tasdeeq_milkiat_pemuda_khasra' => 'nullable|integer',
            'tasdeeq_milkiat_pemuda_khasra_badrat' => 'nullable|integer',
            'tasdeeq_milkiat_qabza_kasht_khasra' => 'nullable|integer',
            'tasdeeq_milkiat_qabza_kasht_badrat' => 'nullable|integer',
            'tasdeeq_shajra_nasab_guri' => 'nullable|integer',
            'tasdeeq_shajra_nasab_badrat' => 'nullable|integer',
            'muqabala_khatoni_chomanda' => 'nullable|integer',
            'muqabala_khatoni_chomanda_badrat' => 'nullable|integer',
            'tabsara' => 'nullable|string',
            'operator_id' => 'nullable|integer',
        ]);

        // Keep IDs as they are
        $validated['zila_nam'] = $validated['zila_id'];
        $validated['tehsil_nam'] = $validated['tehsil_id'];
        $validated['moza_nam'] = $validated['moza_id'];
        unset($validated['zila_id'], $validated['tehsil_id'], $validated['moza_id']);

        $validated['operator_id'] = session('operator_id');

        DB::table($this->table)->where('id', $id)->update($validated);

        return redirect()->route('partal.index')
                         ->with('success', 'Record updated successfully.');
    }

    // Delete a record
    public function destroy($id)
    {
        DB::table($this->table)->where('id', $id)->delete();
        return redirect()->route('partal.index')
                         ->with('success', 'Record deleted successfully.');
    }

    // DataTables server-side processing
    public function datatable(Request $request)
    {
        $columns = [
            'partal.id',
            'districts.districtNameUrdu',
            'tehsils.tehsilNameUrdu',
            'mozas.mozaNameUrdu',
            'partal.patwari_nam',
            'partal.ahalkar_nam',
            'partal.tareekh_partal',
            'partal.tasdeeq_milkiat_pemuda_khasra',
            'partal.tasdeeq_milkiat_pemuda_khasra_badrat',
            'partal.tasdeeq_milkiat_qabza_kasht_khasra',
            'partal.tasdeeq_milkiat_qabza_kasht_badrat',
            'partal.tasdeeq_shajra_nasab_guri',
            'partal.tasdeeq_shajra_nasab_badrat',
            'partal.muqabala_khatoni_chomanda',
            'partal.muqabala_khatoni_chomanda_badrat',
            'partal.tabsara'
        ];

        $query = DB::table($this->table)
            ->leftJoin('employees as emp_ahalkar', 'partal.ahalkar_nam', '=', 'emp_ahalkar.nam')
            ->leftJoin('employee_type as et_ahalkar', 'emp_ahalkar.ahalkar_type', '=', 'et_ahalkar.ahalkar_type_id')
            ->leftJoin('employees as emp_patwari', 'partal.patwari_nam', '=', 'emp_patwari.nam')
            ->leftJoin('employee_type as et_patwari', 'emp_patwari.ahalkar_type', '=', 'et_patwari.ahalkar_type_id')
            ->leftJoin('districts', 'partal.zila_nam', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'partal.tehsil_nam', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'partal.moza_nam', '=', 'mozas.mozaId');

        // Role-based filtering
        if (session('role_id') == 2) {
            $query->where('districts.districtId', session('zila_id'))
                  ->where('tehsils.tehsilId', session('tehsil_id'));
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('partal.id', 'like', '%' . $search . '%')
                  ->orWhere('districts.districtNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('tehsils.tehsilNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('mozas.mozaNameUrdu', 'like', '%' . $search . '%')
                  ->orWhere('partal.patwari_nam', 'like', '%' . $search . '%')
                  ->orWhere('partal.ahalkar_nam', 'like', '%' . $search . '%')
                  ->orWhere('partal.tabsara', 'like', '%' . $search . '%');
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
            $query->orderBy('partal.id', 'desc');
        }

        // Pagination
        $totalRecords = $query->count();
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;
        $query->skip($start)->take($length);

        $records = $query->select(
            'partal.*',
            'et_ahalkar.ahalkar_title as ahalkar_title',
            'et_patwari.ahalkar_title as patwari_title',
            'districts.districtNameUrdu as districtNameUrdu',
            'tehsils.tehsilNameUrdu as tehsilNameUrdu',
            'mozas.mozaNameUrdu as mozaNameUrdu'
        )->get();

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'id' => $record->id,
                'districtNameUrdu' => $record->districtNameUrdu,
                'tehsilNameUrdu' => $record->tehsilNameUrdu,
                'mozaNameUrdu' => $record->mozaNameUrdu,
                'patwari_nam' => $record->patwari_nam . ($record->patwari_title ? ' ' . $record->patwari_title : ''),
                'ahalkar_nam' => $record->ahalkar_nam . ($record->ahalkar_title ? ' ' . $record->ahalkar_title : ''),
                'tareekh_partal' => $record->tareekh_partal ? date('d-m-Y', strtotime($record->tareekh_partal)) : '',
                'tasdeeq_milkiat_pemuda_khasra' => $record->tasdeeq_milkiat_pemuda_khasra ?? '',
                'tasdeeq_milkiat_pemuda_khasra_badrat' => $record->tasdeeq_milkiat_pemuda_khasra_badrat ?? '',
                'tasdeeq_milkiat_qabza_kasht_khasra' => $record->tasdeeq_milkiat_qabza_kasht_khasra ?? '',
                'tasdeeq_milkiat_qabza_kasht_badrat' => $record->tasdeeq_milkiat_qabza_kasht_badrat ?? '',
                'tasdeeq_shajra_nasab_guri' => $record->tasdeeq_shajra_nasab_guri ?? '',
                'tasdeeq_shajra_nasab_badrat' => $record->tasdeeq_shajra_nasab_badrat ?? '',
                'muqabala_khatoni_chomanda' => $record->muqabala_khatoni_chomanda ?? '',
                'muqabala_khatoni_chomanda_badrat' => $record->muqabala_khatoni_chomanda_badrat ?? '',
                'tabsara' => $record->tabsara ?? '',
                'actions' => ($record->operator_id == session('operator_id')) ? '<a href="' . route('partal.edit', $record->id) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> ترمیم</a>' : ''
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }
}

