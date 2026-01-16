<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemBackupController extends Controller
{
    public function index()
    {
        $files = collect(\Storage::files('backups'))
            ->sortDesc();

        return view('system.backup', [
            'lastBackup' => $files->first(),
            'count' => $files->count(),
            'files' => $files,
        ]);
    }

    public function run()
    {
        \Artisan::call('db:backup');

        return back()->with('success', 'Backup berhasil dijalankan.');
    }

    public function download($file)
    {
        return \Storage::download("backups/{$file}");
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file'
        ]);

        $path = $request->file('backup_file')->storeAs('backups', 'restore.sql');

        // restore logic via shell
        exec("mysql -u root absensi_fingerprint < " . storage_path("app/{$path}"));

        return back()->with('success', 'Database berhasil direstore.');
    }
}
