<?php

namespace App\Services;

use App\Models\Blockchain;
use App\Models\User;
use App\Models\UserBlockchainWallet;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class UserWalletProvisionerService
{
    /**
     * Provision deposit wallet addresses for all blockchains for the given user.
     * Generates wallets for all blockchains regardless of whether they are enabled or disabled.
     *
     * @param User $user
     * @return void
     */
    public function provisionWallets(User $user): void
    {
        $blockchains = Blockchain::all();
        if ($blockchains->isEmpty()) {
            return;
        }

        $existingWallets = UserBlockchainWallet::where('user_id', $user->id)
            ->get()
            ->keyBy('blockchain_id');

        $missingChains = $blockchains->filter(function ($bc) use ($existingWallets) {
            return !$existingWallets->has($bc->id);
        });

        if ($missingChains->isEmpty()) {
            return;
        }

        // Check if the user already has an EVM wallet address across any EVM chain
        $evmAddress = null;
        $evmEncryptedPrivateKey = null;

        foreach ($blockchains as $bc) {
            if ($bc->isEvm() && $existingWallets->has($bc->id)) {
                $existingEvm = $existingWallets->get($bc->id);
                $evmAddress = $existingEvm->address;
                $evmEncryptedPrivateKey = $existingEvm->private_key;
                break;
            }
        }

        foreach ($missingChains as $blockchain) {
            try {
                $address = null;
                $encryptedPrivateKey = null;

                if ($blockchain->isEvm()) {
                    if (!$evmAddress || !$evmEncryptedPrivateKey) {
                        $evmWallet = (new EthereumWalletGeneratorService())->generateWallet();
                        $evmAddress = $evmWallet['address'];
                        $evmEncryptedPrivateKey = Crypt::encryptString($evmWallet['private_key']);
                    }
                    $address = $evmAddress;
                    $encryptedPrivateKey = $evmEncryptedPrivateKey;
                } elseif ($blockchain->code === 'solana') {
                    $solanaWallet = (new SolanaWalletGeneratorService())->generateWallet();
                    $address = $solanaWallet['address'];
                    $encryptedPrivateKey = Crypt::encryptString($solanaWallet['private_key']);

                    // Sync legacy user columns
                    $user->solana_wallet_address = $address;
                    $user->solana_private_key = $encryptedPrivateKey;
                    $user->save();
                } elseif ($blockchain->code === 'tron' || $blockchain->isTron()) {
                    $tronWallet = (new TronWalletGeneratorService())->generateWallet();
                    $address = $tronWallet['address'];
                    $encryptedPrivateKey = Crypt::encryptString($tronWallet['private_key']);
                } elseif ($blockchain->code === 'bitcoin' || $blockchain->isBitcoin()) {
                    $btcWallet = (new BitcoinWalletGeneratorService())->generateWallet();
                    $address = $btcWallet['address'];
                    $encryptedPrivateKey = Crypt::encryptString($btcWallet['private_key']);
                } else {
                    Log::warning("No wallet generator available for blockchain code: {$blockchain->code}");
                    continue;
                }

                if ($address && $encryptedPrivateKey) {
                    UserBlockchainWallet::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'blockchain_id' => $blockchain->id,
                        ],
                        [
                            'address' => $address,
                            'private_key' => $encryptedPrivateKey,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error("Failed to provision wallet for user #{$user->id} on blockchain [{$blockchain->code}]: " . $e->getMessage());
            }
        }
    }
}
