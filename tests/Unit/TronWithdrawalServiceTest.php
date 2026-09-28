<?php

namespace Tests\Unit;

use App\Models\Blockchain;
use App\Models\Withdrawal;
use App\Services\TronWithdrawalService;
use Tests\TestCase;

class TronWithdrawalServiceTest extends TestCase
{
    public function test_process_payout_requires_configured_master_wallet(): void
    {
        $withdrawal = new class extends Withdrawal {
            public function __construct()
            {
            }

            public $structured_data;
            public $blockchain;
        };
        $withdrawal->structured_data = json_encode([
            'wallet_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
            'currency' => 'TRX',
            'network' => 'TRON',
        ]);

        $blockchain = new Blockchain([
            'code' => 'tron',
            'name' => 'TRON',
            'master_wallet_address' => null,
            'master_private_key' => null,
        ]);
        $withdrawal->blockchain = $blockchain;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('TRON Master Wallet is not configured.');

        (new TronWithdrawalService())->processPayout($withdrawal);
    }
}
