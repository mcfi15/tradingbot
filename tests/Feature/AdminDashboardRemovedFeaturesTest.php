<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminDashboardRemovedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_loads_without_referencing_removed_features(): void
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
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_user_detail_loads_without_referencing_removed_features(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password123'),
            'otp_verified_at' => now(),
        ]);

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.detail', $user->id));

        $response->assertStatus(200);
    }

    public function test_user_dashboard_loads_without_referencing_removed_features(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => ['converted_amount' => 1, 'exchange_rate' => 1],
            ], 200),
        ]);

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
            ->get(route('user.dashboard'));

        $response->assertStatus(200);
    }
}
