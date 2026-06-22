<?php

namespace App\Console\Commands;

use App\Services\OtpService;
use Illuminate\Console\Command;

class CleanupExpiredOtps extends Command
{
    protected $signature = 'otp:cleanup';

    protected $description = 'Delete expired unused OTP codes';

    public function __construct(
        protected OtpService $otpService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Cleaning up expired OTP codes...');

        try {
            $this->otpService->cleanupExpiredOtps();
            $this->info('✓ Expired OTP codes deleted successfully!');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Cleanup failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
