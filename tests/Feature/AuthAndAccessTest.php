<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AuthAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_log_in_with_username_and_password(): void
    {
        $user = User::query()->create([
            'name' => 'Owner User',
            'username' => 'owner-user',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'owner-user',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_failed_login_is_logged(): void
    {
        User::query()->create([
            'name' => 'Owner User',
            'username' => 'owner-user',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'owner-user',
            'password' => 'wrong-secret',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'user.login_failed',
            'message' => 'Failed login attempt.',
        ]);

        $log = ActivityLog::query()->where('action', 'user.login_failed')->latest()->firstOrFail();
        $this->assertSame('owner-user', $log->metadata['username']);
    }

    public function test_security_headers_are_set_on_guest_and_authenticated_pages(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $guestResponse = $this->get('/login');
        $guestResponse->assertHeader('X-Frame-Options', 'DENY');
        $guestResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $guestResponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $guestResponse->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $guestResponse->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');

        $authResponse = $this->actingAs($owner)->get('/dashboard');
        $authResponse->assertHeader('Content-Security-Policy');
        $authResponse->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_staff_cannot_open_owner_only_pages(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'username' => 'staff-user',
        ]);

        $this->actingAs($staff)->get('/reports')->assertForbidden();
        $this->actingAs($staff)->get('/settings')->assertForbidden();
        $this->actingAs($staff)->get('/categories')->assertForbidden();
        $this->actingAs($staff)->get('/activity')->assertForbidden();
        $this->actingAs($staff)->get('/imports/products')->assertForbidden();
        $this->actingAs($staff)->get('/exports/products')->assertForbidden();
    }

    public function test_owner_can_access_new_management_pages(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'username' => 'owner-admin',
        ]);

        $this->actingAs($owner)->get('/categories')->assertOk();
        $this->actingAs($owner)->get('/activity')->assertOk();
        $this->actingAs($owner)->get('/imports/products')->assertOk();
        $this->actingAs($owner)->get('/exports/products')->assertOk();
    }

    public function test_staff_can_view_alerts_but_cannot_manage_imports(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'username' => 'alerts-staff',
        ]);

        $this->actingAs($staff)->get('/alerts')->assertOk();

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            "name,sku,category,cost,selling_price,quantity,min_stock,description\nSample,SKU-1,General,1.00,2.00,5,1,Test"
        );

        $this->actingAs($staff)->post('/imports/products', [
            'csv_file' => $file,
        ])->assertForbidden();
    }
}
