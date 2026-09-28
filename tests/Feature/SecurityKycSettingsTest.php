<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityKycSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_settings_page_does_not_display_removed_kyc_rules(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.security.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Require KYC for: Trading');
        $response->assertDontSee('Require KYC for: Investment');
        $response->assertDontSee('Require KYC for: Capital Instruments');
        $response->assertSee('Require KYC for: Deposits');
        $response->assertSee('Require KYC for: Withdrawal');
    }

    public function test_security_settings_update_only_saves_active_kyc_rules(): void
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_update',
            'email' => 'admin_update@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.security.update'), [
                'email_verification' => 'enabled',
                'google_recaptcha' => 'disabled',
                'require_strong_password' => 'enabled',
                'login_otp' => 'disabled',
                'kyc' => [
                    'Deposits' => 'enabled',
                    'Withdrawal' => 'enabled',
                    'Trading' => 'enabled',
                ],
            ]);

        $response->assertRedirect();
        
        $savedKyc = json_decode(getSetting('kyc'), true) ?? [];
        $names = array_column($savedKyc, 'name');

        $this->assertNotContains('Trading', $names);
        $this->assertNotContains('Investment', $names);
        $this->assertNotContains('Capital Instruments', $names);
        $this->assertContains('Deposits', $names);
        $this->assertContains('Withdrawal', $names);
    }
}
