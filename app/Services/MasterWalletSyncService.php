<?php

namespace App\Services;

use App\Models\Blockchain;
use Illuminate\Support\Facades\Crypt;

class MasterWalletSyncService
{
    /**
     * Create/link master wallets for blockchains that do not have one yet.
     *
     * @return array<string, mixed>
     */
    public function syncMissingMasterWallets(): array
    {
        $blockchains = Blockchain::get();
        $updatedChains = [];
        $groupedUpdates = [
            'evm' => [],
            'solana' => [],
            'tron' => [],
            'bitcoin' => [],
        ];

        // 1) Resolve EVM credentials.
        $evmAddress = null;
        $evmPrivateKey = null;

        foreach ($blockchains as $bc) {
            if ($bc->isEvm() && $bc->master_wallet_address && $bc->master_private_key) {
                $evmAddress = $bc->master_wallet_address;
                $evmPrivateKey = $bc->master_private_key;
                break;
            }
        }

        if (!$evmAddress || !$evmPrivateKey) {
            $evmWallet = (new EthereumWalletGeneratorService())->generateWallet();
            $evmAddress = $evmWallet['address'];
            $evmPrivateKey = Crypt::encryptString($evmWallet['private_key']);
        }

        foreach ($blockchains as $bc) {
            if ($bc->isEvm() && empty($bc->master_wallet_address)) {
                $bc->master_wallet_address = $evmAddress;
                $bc->master_private_key = $evmPrivateKey;
                $bc->save();
                $updatedChains[] = $bc->name;
                $groupedUpdates['evm'][] = $bc->name;
            }
        }

        // 2) Ensure Solana wallet exists.
        $solanaBc = $blockchains->where('code', 'solana')->first();
        if ($solanaBc && empty($solanaBc->master_wallet_address)) {
            $solanaWallet = (new SolanaWalletGeneratorService())->generateWallet();
            $solanaBc->master_wallet_address = $solanaWallet['address'];
            $solanaBc->master_private_key = Crypt::encryptString($solanaWallet['private_key']);
            $solanaBc->save();
            $updatedChains[] = $solanaBc->name;
            $groupedUpdates['solana'][] = $solanaBc->name;
        }

        // 3) Ensure Tron wallet exists.
        $tronBc = $blockchains->where('code', 'tron')->first();
        if ($tronBc && empty($tronBc->master_wallet_address)) {
            $tronWallet = (new TronWalletGeneratorService())->generateWallet();
            $tronBc->master_wallet_address = $tronWallet['address'];
            $tronBc->master_private_key = Crypt::encryptString($tronWallet['private_key']);
            $tronBc->save();
            $updatedChains[] = $tronBc->name;
            $groupedUpdates['tron'][] = $tronBc->name;
        }

        // 4) Ensure Bitcoin wallet exists.
        $bitcoinBc = $blockchains->where('code', 'bitcoin')->first();
        if ($bitcoinBc && empty($bitcoinBc->master_wallet_address)) {
            $bitcoinWallet = (new BitcoinWalletGeneratorService())->generateWallet();
            $bitcoinBc->master_wallet_address = $bitcoinWallet['address'];
            $bitcoinBc->master_private_key = Crypt::encryptString($bitcoinWallet['private_key']);
            $bitcoinBc->save();
            $updatedChains[] = $bitcoinBc->name;
            $groupedUpdates['bitcoin'][] = $bitcoinBc->name;
        }

        return [
            'updated_chains' => $updatedChains,
            'grouped_updates' => $groupedUpdates,
            'updated_count' => count($updatedChains),
        ];
    }
}
