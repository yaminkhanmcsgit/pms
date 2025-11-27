<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = DB::table('settings')->first();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = [
            'footer_text' => $request->footer_text,
        ];
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = $logo->store('public/logo');
            $data['logo_path'] = str_replace('public/', 'storage/', $path);
        }
        $setting = DB::table('settings')->first();
        if ($setting) {
            DB::table('settings')->where('id', $setting->id)->update($data);
        } else {
            DB::table('settings')->insert($data);
        }
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
