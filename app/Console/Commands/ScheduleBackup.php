<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Console\Command;

class ScheduleBackup extends Command
{
    protected $signature = 'backup:schedule {--destinations=backblaze}';

    protected $description = 'Run scheduled database backup';

    public function __construct(
        protected BackupService $backupService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $destinations = explode(',', $this->option('destinations') ?? 'backblaze');

        $super_admin = User::where('role', 'super_admin')->first();

        if (!$super_admin) {
            $this->error('No super_admin user found to associate with backup.');
            return self::FAILURE;
        }

        $this->info('Running scheduled backup...');

        try {
            $backup = $this->backupService->createBackup($super_admin, 'scheduled', $destinations);

            $this->info("✓ Scheduled backup completed!");
            $this->info("Backup ID: {$backup->id}");
            $this->info("Name: {$backup->name}");
            $this->info("Destinations: " . implode(', ', $destinations));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Scheduled backup failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
