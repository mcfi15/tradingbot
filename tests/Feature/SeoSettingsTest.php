<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeoSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_settings_page_loads_with_bot_and_copy_trading_copy(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_seo',
            'email' => 'admin_seo@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.seo'));

        $response->assertStatus(200);
        $response->assertDontSee('stocks, ETFs');
    }

    public function test_updating_seo_settings_with_site_name_placeholder(): void
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_seo_update',
            'email' => 'admin_seo_update@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.seo.update'), [
                'seo_description' => 'Automate your crypto trading with algorithmic AI bots on :site_name.',
                'seo_keywords' => 'copy trading, AI trading bots, :site_name',
                'social_title' => ':site_name | Automated AI Bot & Copy Trading Platform',
                'social_description' => 'Deploy automated AI trading bots in real time on :site_name.',
                'search_engine_indexing' => '1',
                'social_media' => [
                    'twitter' => 'https://twitter.com/user',
                    'facebook' => 'https://facebook.com/user',
                    'instagram' => 'https://instagram.com/user',
                    'telegram' => 'https://t.me/user',
                ],
            ]);

        $response->assertRedirect();

        $this->assertEquals('Automate your crypto trading with algorithmic AI bots on :site_name.', \App\Models\Setting::where('key', 'seo_description')->value('value'));
        $this->assertEquals(':site_name | Automated AI Bot & Copy Trading Platform', \App\Models\Setting::where('key', 'social_title')->value('value'));

        $savedSocial = json_decode(\App\Models\Setting::where('key', 'social_media')->value('value'), true) ?? [];
        $this->assertEquals('https://twitter.com/user', $savedSocial['twitter'] ?? null);
        $this->assertEquals('https://t.me/user', $savedSocial['telegram'] ?? null);
    }

    public function test_front_layout_parses_site_name_in_meta_tags(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        updateSetting('name', 'Foyana');
        updateSetting('seo_description', 'Trade on :site_name with AI bots.');

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Trade on Foyana with AI bots.');
    }
}
