<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    public function index()
    {
        $backups = Backup::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.backups', [
            'backups' => $backups,
        ]);
    }

    public function create(Request $request)
    {
        $backup = $this->backupService->createBackup(auth()->user());

        return back()->with('message', 'Backup created successfully.');
    }

    public function restore(Request $request, Backup $backup)
    {
        $request->validate(['confirm' => 'required|accepted']);

        $success = $this->backupService->restoreFromBackup($backup, auth()->user());

        if (!$success) {
            return back()->withErrors('Failed to restore backup.');
        }

        return back()->with('message', 'Backup restored successfully.');
    }

    public function delete(Backup $backup)
    {
        $this->backupService->deleteBackup($backup);

        return back()->with('message', 'Backup deleted successfully.');
    }

    public function download(Backup $backup)
    {
        if (!$backup->backup_path) {
            return back()->withErrors('Backup file not found.');
        }

        return response()->download(storage_path('app/' . $backup->backup_path));
    }
}
