<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Blockchain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepositControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_reset_all_master_wallets()
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $blockchainA = Blockchain::create([
            'name' => 'Test Chain A',
            'code' => 'test-chain-a',
            'rpc_url' => 'https://example.test/a',
            'status' => 'enabled',
            'master_wallet_address' => '0xabc123',
            'master_private_key' => 'private-key-a',
        ]);

        $blockchainB = Blockchain::create([
            'name' => 'Test Chain B',
            'code' => 'test-chain-b',
            'rpc_url' => 'https://example.test/b',
            'status' => 'enabled',
            'master_wallet_address' => '0xdef456',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.deposits.master-wallets.reset'), [
                'password' => 'password123',
            ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('blockchains', [
            'id' => $blockchainA->id,
            'master_wallet_address' => null,
            'master_private_key' => null,
        ]);

        $this->assertDatabaseHas('blockchains', [
            'id' => $blockchainB->id,
            'master_wallet_address' => null,
            'master_private_key' => null,
        ]);
    }
}
