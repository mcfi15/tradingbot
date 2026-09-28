<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class DepositTokenBlockchainHelperTest extends TestCase
{
    public function test_it_groups_tokens_by_symbol_with_blockchain_wallets(): void
    {
        $ethereum = new class {
            public $id = 1;
            public $name = 'Ethereum';
            public $code = 'ethereum';
            public $tokens;
        };

        $solana = new class {
            public $id = 2;
            public $name = 'Solana';
            public $code = 'solana';
            public $tokens;
        };

        $usdt = new class {
            public $symbol = 'USDT';
            public $name = 'Tether';
            public $priority = 1;
            public $logo = null;
        };

        $usdc = new class {
            public $symbol = 'USDC';
            public $name = 'USD Coin';
            public $priority = 2;
            public $logo = null;
        };

        $ethereum->tokens = collect([$usdt, $usdc]);
        $solana->tokens = collect([$usdt]);

        $userWallets = collect([
            (object) ['blockchain_id' => 1, 'address' => '0xabc123'],
            (object) ['blockchain_id' => 2, 'address' => 'solwallet123'],
        ]);

        $result = buildDepositTokenBlockchainMap(collect([$ethereum, $solana]), $userWallets);

        $this->assertCount(2, $result);
        $this->assertSame('USDT', $result[0]['token']['symbol']);
        $this->assertSame('Ethereum', $result[0]['blockchains'][0]['blockchain_name']);
        $this->assertSame('0xabc123', $result[0]['blockchains'][0]['wallet_address']);
        $this->assertSame('Solana', $result[0]['blockchains'][1]['blockchain_name']);
        $this->assertSame('solwallet123', $result[0]['blockchains'][1]['wallet_address']);
        $this->assertSame('USDC', $result[1]['token']['symbol']);
        $this->assertSame('Ethereum', $result[1]['blockchains'][0]['blockchain_name']);
        $this->assertSame('0xabc123', $result[1]['blockchains'][0]['wallet_address']);
    }
}
