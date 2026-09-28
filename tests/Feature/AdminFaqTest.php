<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminFaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_faq_settings_page_loads_successfully(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin_faq',
            'email' => 'admin_faq@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        Faq::create([
            'question' => 'How does copy trading work?',
            'answer' => 'Copy trading allows you to automatically mirror trades from vetted lead traders.',
            'category' => 'Copy Trading',
            'sort_order' => 1,
            'status' => 1,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.faq.index'));

        $response->assertStatus(200);
        $response->assertSee('How does copy trading work?');
    }
}
