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
        $uploadDir = base_path('assets/logo');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $data = [
            'footer_text' => $request->footer_text,
        ];
        $setting = DB::table('settings')->first();
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting && $setting->logo_path) {
                $fullPath = base_path('assets/logo/' . $setting->logo_path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadDir, $filename);
            $data['logo_path'] = $filename;
        }
        if ($setting) {
            DB::table('settings')->where('id', $setting->id)->update($data);
        } else {
            DB::table('settings')->insert($data);
        }
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
