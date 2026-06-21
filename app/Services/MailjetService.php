<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Mail;

class MailjetService
{
    public function testConnection(): bool
    {
        try {
            $settings = AppSetting::first();

            if (!$settings || !$settings->mailjet_api_key || !$settings->mailjet_api_secret) {
                return false;
            }

            // Test by making a simple API call to Mailjet
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mailjet.com/v3/REST/user');
            curl_setopt($ch, CURLOPT_USERPWD, $settings->mailjet_api_key . ':' . $settings->mailjet_api_secret);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFromEmail(): string
    {
        $settings = AppSetting::first();
        return $settings?->mailjet_from_email ?? config('mail.from.address', 'noreply@stockscan.local');
    }

    public function getFromName(): string
    {
        $settings = AppSetting::first();
        return $settings?->mailjet_from_name ?? config('mail.from.name', 'StockScan');
    }

    public function isNotificationsEnabled(): bool
    {
        $settings = AppSetting::first();
        return $settings?->notifications_enabled ?? true;
    }
}
