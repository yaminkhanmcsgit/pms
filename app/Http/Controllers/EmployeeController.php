<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index() {
        $query = DB::table('employees')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', 'employees.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'employees.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'employees.moza_id', '=', 'mozas.mozaId');

        if (session('role_id') == 2) {
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            $query->where('employees.zila_id', session('zila_id'))
                  ->whereIn('employees.tehsil_id', $assignedTehsils);
        }

        $employees = $query->select(
                'employees.*',
                'employee_type.ahalkar_title as employee_type_title',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->get();
        return view('employees.index', compact('employees'));
    }

    public function apiIndex() {
        $query = DB::table('employees')
            ->leftJoin('employee_type', 'employees.ahalkar_type', '=', 'employee_type.ahalkar_type_id')
            ->leftJoin('districts', 'employees.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'employees.tehsil_id', '=', 'tehsils.tehsilId')
            ->leftJoin('mozas', 'employees.moza_id', '=', 'mozas.mozaId');

        if (session('role_id') == 2) {
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            $query->where('employees.zila_id', session('zila_id'))
                  ->whereIn('employees.tehsil_id', $assignedTehsils);
        }

        $employees = $query->select(
                'employees.*',
                'employee_type.ahalkar_title as employee_type_title',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name',
                'mozas.mozaNameUrdu as moza_name'
            )
            ->get();
        // actually
        // $response = response();
        //return $response->json(['employees' => $employees]);

        return response()->json(['employees' => $employees]);
    }

    public function create()
{
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = DB::table('districts')->orderBy('districtId')->get();
        $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
    } else {
        $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->whereIn('tehsilId', $assignedTehsils)->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->whereIn('tehsilId', $assignedTehsils)->orderBy('mozaId')->get();
    }
    $employee_types = DB::table('employee_type')->orderBy('ahalkar_type_id')->get();
    return view('employees.create', compact('districts', 'tehsils', 'mozas', 'employee_types', 'role_id'));
}

    public function store(Request $request) {

        //echo "<h1>Hello</h1>";exit;
        $roleId = (int) session('role_id');

        $validated = $request->validate([
            'nam' => 'required|string|max:150',
            'walid_ka_nam' => 'nullable|string|max:150',
            'zila_id' => $roleId === 1 ? 'required|integer|exists:districts,districtId' : 'nullable|integer',
            'tehsil_id' => 'required|integer|exists:tehsils,tehsilId',
            'moza_id' => 'required|integer|exists:mozas,mozaId',
            
            'pata' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'cnic' => 'nullable|string|max:20',
            'darja_taleem' => 'nullable|string|max:100',
            'ahalkar_type' => 'required|integer|exists:employee_type,ahalkar_type_id',
            'tareekh_shamil' => 'required|date',
        ]);

        if ($roleId !== 1) {
            $validated['zila_id'] = (int) session('zila_id');
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            if (!in_array((string) $validated['tehsil_id'], $assignedTehsils, true)) {
                return back()->withInput()->withErrors(['tehsil_id' => 'منتخب تحصیل آپ کو الاٹ نہیں ہے۔']);
            }

            $mozaAllowed = DB::table('mozas')
                ->where('mozaId', $validated['moza_id'])
                ->where('tehsilId', $validated['tehsil_id'])
                ->exists();

            if (!$mozaAllowed) {
                return back()->withInput()->withErrors(['moza_id' => 'منتخب موضع درست نہیں ہے۔']);
            }
        }

        DB::table('employees')->insert([
            'nam' => $validated['nam'],
            'walid_ka_nam' => $validated['walid_ka_nam'] ?? null,
            'zila_id' => $validated['zila_id'] ?? null,
            'tehsil_id' => $validated['tehsil_id'] ?? null,
            'moza_id' => $validated['moza_id'],
            'pata' => $validated['pata'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'cnic' => $validated['cnic'] ?? null,
            'darja_taleem' => $validated['darja_taleem'] ?? null,
            'ahalkar_type' => $validated['ahalkar_type'],
            'tareekh_shamil' => $validated['tareekh_shamil'],
            'created_at' => now(),
        ]);

        return redirect()->route('employees.index')->with('success','Employee created successfully.');
    }

    public function edit($id) {
    $employee = DB::table('employees')
        ->leftJoin('districts', 'employees.zila_id', '=', 'districts.districtId')
        ->leftJoin('tehsils', 'employees.tehsil_id', '=', 'tehsils.tehsilId')
        ->leftJoin('mozas', 'employees.moza_id', '=', 'mozas.mozaId')
        ->select(
            'employees.*',
            'districts.districtNameUrdu as district_name',
            'tehsils.tehsilNameUrdu as tehsil_name',
            'mozas.mozaNameUrdu as moza_name'
        )
        ->where('employees.id', $id)
        ->first();

    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = DB::table('districts')->orderBy('districtId')->get();
        $tehsils   = DB::table('tehsils')->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->orderBy('mozaId')->get();
    } else {
        $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->whereIn('tehsilId', $assignedTehsils)->orderBy('tehsilId')->get();
        $mozas     = DB::table('mozas')->whereIn('tehsilId', $assignedTehsils)->orderBy('mozaId')->get();
    }
    $employee_types = DB::table('employee_type')->orderBy('ahalkar_type_id')->get();
    return view('employees.edit', compact('employee', 'districts', 'tehsils',  'employee_types', 'role_id','mozas'));
    }

    public function update(Request $request, $id) {
        $roleId = (int) session('role_id');

        $request->validate([
            'nam' => 'required|string|max:150',
            'walid_ka_nam' => 'nullable|string|max:150',
            'zila_id' => $roleId === 1 ? 'required|integer|exists:districts,districtId' : 'nullable|integer',
            'tehsil_id' => 'required|integer|exists:tehsils,tehsilId',
            'moza_id' => 'nullable|integer',
            'pata' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'cnic' => 'nullable|string|max:20',
            'darja_taleem' => 'nullable|string|max:100',
            'ahalkar_type' => 'nullable|string|max:100',
            'tareekh_shamil' => 'nullable|date',
        ]);

        if ($roleId !== 1) {
            $assignedTehsils = array_values(array_filter(array_map('trim', explode(',', (string) session('tehsil_id')))));
            if (!in_array((string) $request->tehsil_id, $assignedTehsils, true)) {
                return back()->withInput()->withErrors(['tehsil_id' => 'منتخب تحصیل آپ کو الاٹ نہیں ہے۔']);
            }
        }

        DB::table('employees')->where('id', $id)->update([
            'nam' => $request->nam,
            'walid_ka_nam' => $request->walid_ka_nam,
            'zila_id' => $roleId === 1 ? $request->zila_id : session('zila_id'),
            'tehsil_id' => $request->tehsil_id,
            'moza_id' => $request->moza_id,
            'pata' => $request->pata,
            'phone' => $request->phone,
            'cnic' => $request->cnic,
            'darja_taleem' => $request->darja_taleem,
            'ahalkar_type' => $request->ahalkar_type,
            'tareekh_shamil' => $request->tareekh_shamil,
        ]);

        return redirect()->route('employees.index')->with('success','Employee updated successfully.');
    }
}
