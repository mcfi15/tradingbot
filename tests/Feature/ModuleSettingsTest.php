<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ModuleSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_modules_settings_page_does_not_display_loan_or_p2p_transfer(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_modules',
            'email' => 'admin_modules@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.modules.index'));

        $response->assertStatus(200);
        $response->assertDontSee('id="module-card-loan_module"', false);
        $response->assertDontSee('id="module-card-p2p_transfer_module"', false);
        $response->assertSee('id="module-card-trading_bot_module"', false);
        $response->assertSee('id="module-card-file_manager_module"', false);
        $response->assertSee('id="module-card-kyc_module"', false);
        $response->assertSee('id="module-card-copy_trading_module"', false);
    }

    public function test_updating_modules_does_not_reintroduce_loan_or_p2p_transfer(): void
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_modules_update',
            'email' => 'admin_modules_update@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.settings.modules.update'), [
                'modules' => [
                    'trading_bot_module' => 'enabled',
                    'file_manager_module' => 'enabled',
                    'kyc_module' => 'enabled',
                    'copy_trading_module' => 'enabled',
                    'loan_module' => 'enabled',
                    'p2p_transfer_module' => 'enabled',
                ],
            ]);

        $response->assertStatus(200);

        $savedModules = json_decode(getSetting('modules'), true) ?? [];
        $this->assertArrayNotHasKey('loan_module', $savedModules);
        $this->assertArrayNotHasKey('p2p_transfer_module', $savedModules);
        $this->assertArrayHasKey('trading_bot_module', $savedModules);
        $this->assertArrayHasKey('kyc_module', $savedModules);
    }
}
