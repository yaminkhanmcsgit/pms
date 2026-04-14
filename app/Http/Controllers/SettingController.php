<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

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

    public function backup()
    {
        $filename = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $storagePath = storage_path('app/backups');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = $storagePath . '/' . $filename;

        // Create backup using PHP (mysldump disabled on server)
        $this->createPhpBackup($filePath);

        // Create ZIP file
        $zipFilename = str_replace('.sql', '.zip', $filename);
        $zipPath = storage_path('app/backups/' . $zipFilename);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($filePath, $filename);
            $zip->close();
            // Delete the unzipped SQL file
            unlink($filePath);
        }

        if (!file_exists($zipPath)) {
            return redirect()->back()->with('error', 'Failed to create database backup!');
        }

        return response()->download($zipPath, $zipFilename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function createPhpBackup($filePath)
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: " . $dbName . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = reset($table);
            $sql .= "-- Table: $tableName\n";
            
            // Get table structure
            $create = DB::select("SHOW CREATE TABLE `$tableName`");
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= $create[0]->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $sql .= "INSERT INTO `$tableName` VALUES (" . implode(', ', $values) . ");\n";
                }
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($filePath, $sql);
    }
}
