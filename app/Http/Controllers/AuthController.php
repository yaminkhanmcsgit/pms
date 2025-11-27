<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        //var_dump($request->password);exit;

        $user = DB::table('operators')->where('username', $request->username)->first();
        if ($user && trim($request->password) === trim((string)$user->password)) {
            Session::put('operator_id', $user->id);
            Session::put('operator_name', $user->full_name);
            Session::put('role_id', $user->role_id);
            Session::put('zila_id', $user->zila_id);
            Session::put('tehsil_id', $user->tehsil_id);
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['username' => 'غلط صارف نام یا پاسورڈ'])->withInput();
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }

    public function changePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:4',
            'confirm_password' => 'required|same:new_password',
        ], [
            'old_password.required' => 'پرانا پاسورڈ درج کریں',
            'new_password.required' => 'نیا پاسورڈ درج کریں',
            'new_password.min' => 'نیا پاسورڈ کم از کم 4 حروف کا ہونا چاہیے',
            'confirm_password.required' => 'تصدیق پاسورڈ درج کریں',
            'confirm_password.same' => 'تصدیق پاسورڈ مماثل نہیں',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $operatorId = Session::get('operator_id');
        if (!$operatorId) {
            return response()->json(['errors' => ['آپ لاگ ان نہیں ہیں']], 401);
        }

        $operator = DB::table('operators')->where('id', $operatorId)->first();
        if (!$operator || trim($request->old_password) !== trim((string)$operator->password)) {
            return response()->json(['errors' => ['پرانا پاسورڈ درست نہیں']], 422);
        }

        DB::table('operators')->where('id', $operatorId)->update([
            'password' => trim($request->new_password)
        ]);

        return response()->json(['success' => 'پاسورڈ کامیابی سے تبدیل ہوگیا']);
    }
}
