<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
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

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $operator = DB::table('operators')->where('username', $request->email)->first();
        if (!$operator) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'اس ای میل سے کوئی اکاؤنٹ نہیں ملا']);
            }
            return back()->withErrors(['email' => 'اس ای میل سے کوئی اکاؤنٹ نہیں ملا']);
        }

        $token = bin2hex(random_bytes(32));
        $expires = now()->addHours(1);

        DB::table('operators')->where('id', $operator->id)->update([
            'reset_token' => $token,
            'reset_token_expires' => $expires,
        ]);

        $resetLink = route('reset.password', ['token' => $token]);
        $subject = 'پاسورڈ ری سیٹ کی درخواست';
        $body = "
            <p>ہیلو {$operator->full_name},</p>
            <p>آپ نے پاسورڈ ری سیٹ کی درخواست کی ہے۔ نیچے دیے گئے لنک پر کلک کریں تاکہ اپنا پاسورڈ ری سیٹ کریں:</p>
            <p><a href='{$resetLink}'>پاسورڈ ری سیٹ کریں</a></p>
            <p>یہ لنک 1 گھنٹے تک درست رہے گا۔</p>
            <p>اگر آپ نے یہ درخواست نہیں کی ہے تو اس ای میل کو نظر انداز کریں۔</p>
        ";

        $emailSent = sendEmail($operator->username, $subject, $body);

        if ($request->ajax()) {
            if ($emailSent) {
                return response()->json(['success' => true, 'message' => 'پاسورڈ ری سیٹ کا لنک آپ کے ای میل پر بھیج دیا گیا ہے']);
            } else {
                return response()->json(['success' => false, 'message' => 'ای میل بھیجنے میں خرابی ہوئی ہے']);
            }
        }

        if ($emailSent) {
            return back()->with('success', 'پاسورڈ ری سیٹ کا لنک آپ کے ای میل پر بھیج دیا گیا ہے');
        } else {
            return back()->withErrors(['email' => 'ای میل بھیجنے میں خرابی ہوئی ہے']);
        }
    }

    public function showResetPasswordForm($token)
    {
        $operator = DB::table('operators')->where('reset_token', $token)->where('reset_token_expires', '>', now())->first();
        if (!$operator) {
            return redirect()->route('login')->withErrors(['token' => 'غلط یا ختم شدہ ٹوکن']);
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:4',
            'password_confirmation' => 'required|same:password',
        ]);

        $operator = DB::table('operators')->where('reset_token', $request->token)->where('reset_token_expires', '>', now())->first();
        if (!$operator) {
            return back()->withErrors(['token' => 'غلط یا ختم شدہ ٹوکن']);
        }

        DB::table('operators')->where('id', $operator->id)->update([
            'password' => trim($request->password),
            'reset_token' => null,
            'reset_token_expires' => null,
        ]);

        return redirect()->route('login')->with('success', 'پاسورڈ کامیابی سے ری سیٹ ہوگیا ہے');
    }
}
