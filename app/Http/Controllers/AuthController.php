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
        $subject = 'Password Reset Request';
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;'>
            <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                <h2 style='color: #333; text-align: center; margin-bottom: 20px;'>Password Reset Request</h2>
                <p style='color: #555; font-size: 16px; line-height: 1.6;'>Hello {$operator->full_name},</p>
                <p style='color: #555; font-size: 16px; line-height: 1.6;'>You have requested a password reset. Click the button below to reset your password:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' style='background-color: #28a745; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: bold; display: inline-block; box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3); transition: background-color 0.3s;'>Reset Password</a>
                </div>
                <p style='color: #777; font-size: 14px; text-align: center;'>This link will expire in 1 hour.</p>
                <p style='color: #777; font-size: 14px; text-align: center;'>If you did not request this, please ignore this email.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='color: #999; font-size: 12px; text-align: center;'>This is an automated message. Please do not reply.</p>
            </div>
        </div>
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
