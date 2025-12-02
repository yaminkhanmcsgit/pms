<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    public function index() {
        $query = DB::table('operators')
            ->leftJoin('roles', 'operators.role_id', '=', 'roles.role_id')
            ->leftJoin('districts', 'operators.zila_id', '=', 'districts.districtId')
            ->leftJoin('tehsils', 'operators.tehsil_id', '=', 'tehsils.tehsilId');

        if (session('role_id') == 2) {
            $query->where('operators.zila_id', session('zila_id'))
                  ->where('operators.tehsil_id', session('tehsil_id'));
        }

        $operators = $query->select(
                'operators.*',
                'roles.title as role_title',
                'districts.districtNameUrdu as district_name',
                'tehsils.tehsilNameUrdu as tehsil_name'
            )
            ->get();
        return view('operators.index', compact('operators'));
    }

    public function create() {
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = collect();
        $tehsils   = collect();
    } else {
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
    }
    return view('operators.create', compact('districts', 'tehsils', 'role_id'));
    }

    public function store(Request $request) {
        $request->validate([
            'zila_id' => 'required|integer',
            'tehsil_id' => 'required|integer',
            'username' => 'required|string|max:100|unique:operators',
            'password' => 'required|string|min:6',
            'full_name' => 'nullable|string|max:150',
        ]);

        DB::table('operators')->insert([
            'zila_id' => $request->zila_id,
            'tehsil_id' => $request->tehsil_id,
            'username' => $request->username,
            'password' => $request->password, // WARNING: Plain text, not secure
            'full_name' => $request->full_name,
            'role_id' => 2,
            'created_at' => now(),
        ]);

        return redirect()->route('operators.index')->with('success', 'Operator created successfully.');
    }

    public function edit($id) {
    $operator = DB::table('operators')
        ->leftJoin('districts', 'operators.zila_id', '=', 'districts.districtId')
        ->leftJoin('tehsils', 'operators.tehsil_id', '=', 'tehsils.tehsilId')
        ->select('operators.*', 'districts.districtNameUrdu as district_name', 'tehsils.tehsilNameUrdu as tehsil_name')
        ->where('operators.id', $id)
        ->first();
    $role_id = session('role_id');
    if ($role_id == 1) {
        $districts = collect();
        $tehsils   = collect();
    } else {
        $districts = DB::table('districts')->where('districtId', session('zila_id'))->get();
        $tehsils   = DB::table('tehsils')->where('tehsilId', session('tehsil_id'))->get();
    }
    return view('operators.edit', compact('operator', 'districts', 'tehsils', 'role_id'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'zila_id' => 'required|integer',
            'tehsil_id' => 'required|integer',
            'username' => 'required|string|max:100|unique:operators,username,'.$id,
            'password' => 'nullable|string|min:6',
            'full_name' => 'nullable|string|max:150',
        ]);

        $data = [
            'zila_id' => $request->zila_id,
            'tehsil_id' => $request->tehsil_id,
            'username' => $request->username,
            'full_name' => $request->full_name,
            'role_id' => 2,
        ];

        if(!empty($request->password)) {
            $data['password'] = $request->password; // WARNING: Plain text, not secure
        }

        DB::table('operators')->where('id', $id)->update($data);

        return redirect()->route('operators.index')->with('success', 'Operator updated successfully.');
    }
}
