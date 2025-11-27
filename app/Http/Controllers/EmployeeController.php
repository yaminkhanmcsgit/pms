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
            $query->where('employees.zila_id', session('zila_id'))
                  ->where('employees.tehsil_id', session('tehsil_id'));
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
    $employee_types = DB::table('employee_type')->orderBy('ahalkar_type_id')->get();
    return view('employees.create', compact('districts', 'tehsils', 'mozas', 'employee_types', 'role_id'));
}

    public function store(Request $request) {
        $request->validate([
            'nam' => 'required|string|max:150',
            'walid_ka_nam' => 'nullable|string|max:150',
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            
            'pata' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'cnic' => 'nullable|string|max:20',
            'darja_taleem' => 'nullable|string|max:100',
            'ahalkar_type' => 'nullable|string|max:100',
            'tareekh_shamil' => 'nullable|date',
        ]);

        DB::table('employees')->insert([
            'nam' => $request->nam,
            'walid_ka_nam' => $request->walid_ka_nam,
            'zila_id' => $request->zila_id,
            'tehsil_id' => $request->tehsil_id,
            'moza_id' => $request->moza_id,
            'pata' => $request->pata,
            'phone' => $request->phone,
            'cnic' => $request->cnic,
            'darja_taleem' => $request->darja_taleem,
            'ahalkar_type' => $request->ahalkar_type,
            'tareekh_shamil' => $request->tareekh_shamil,
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
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
        $mozas     = DB::table('mozas')->where('tehsilId', session('tehsil_id'))->get();
    }
    $employee_types = DB::table('employee_type')->orderBy('ahalkar_type_id')->get();
    return view('employees.edit', compact('employee', 'districts', 'tehsils',  'employee_types', 'role_id'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nam' => 'required|string|max:150',
            'walid_ka_nam' => 'nullable|string|max:150',
            'zila_id' => 'nullable|integer',
            'tehsil_id' => 'nullable|integer',
            'moza_id' => 'nullable|integer',
            'pata' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'cnic' => 'nullable|string|max:20',
            'darja_taleem' => 'nullable|string|max:100',
            'ahalkar_type' => 'nullable|string|max:100',
            'tareekh_shamil' => 'nullable|date',
        ]);

        DB::table('employees')->where('id', $id)->update([
            'nam' => $request->nam,
            'walid_ka_nam' => $request->walid_ka_nam,
            'zila_id' => $request->zila_id,
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
