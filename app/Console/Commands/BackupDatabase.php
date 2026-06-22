<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--type=manual} {--destinations=local}';

    protected $description = 'Create a database backup';

    public function __construct(
        protected BackupService $backupService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $type = $this->option('type') ?? 'manual';
        $destinations = explode(',', $this->option('destinations') ?? 'local');

        $super_admin = User::where('role', 'super_admin')->first();

        if (!$super_admin) {
            $this->error('No super_admin user found to associate with backup.');
            return self::FAILURE;
        }

        $this->info("Creating {$type} backup...");

        try {
            $backup = $this->backupService->createBackup($super_admin, $type, $destinations);

            $this->info("✓ Backup created successfully!");
            $this->info("Backup ID: {$backup->id}");
            $this->info("Name: {$backup->name}");
            $this->info("Status: {$backup->status}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Backup failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
