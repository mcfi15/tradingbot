<?php

namespace App\Services;

class SolanaWalletGeneratorService
{
    private static $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Encode a binary string (or byte array) into Base58 format.
     *
     * @param string|array $input
     * @return string
     */
    public static function encode($input): string
    {
        if (is_string($input)) {
            $input = unpack('C*', $input);
        }

        if (!is_array($input) || empty($input)) {
            return '';
        }

        $bytes = array_values($input);
        $length = count($bytes);

        // Count leading zeroes in the byte array
        $zeroes = 0;
        while ($zeroes < $length && $bytes[$zeroes] === 0) {
            $zeroes++;
        }

        // Convert base 256 to base 58
        $size = (int)($length * 138 / 100 + 1);
        $digits = array_fill(0, $size, 0);

        for ($i = 0; $i < $length; $i++) {
            $carry = $bytes[$i];
            for ($j = 0; $j < $size; $j++) {
                $carry += $digits[$j] * 256;
                $digits[$j] = $carry % 58;
                $carry = (int)($carry / 58);
            }
        }

        // Trim leading zeroes in the digits array
        $digitsCount = count($digits);
        $start = $digitsCount - 1;
        while ($start >= 0 && $digits[$start] === 0) {
            $start--;
        }

        // Translate to base58 string
        $output = '';
        for ($i = 0; $i < $zeroes; $i++) {
            $output .= '1';
        }
        for ($i = $start; $i >= 0; $i--) {
            $output .= self::$alphabet[$digits[$i]];
        }

        return $output;
    }

    /**
     * Generate a new Solana wallet (Ed25519 Keypair).
     * Returns an array containing the Base58 public address and the Base58 private key.
     *
     * @return array
     * @throws \Exception
     */
    public function generateWallet(): array
    {
        if (!function_exists('sodium_crypto_sign_keypair')) {
            throw new \Exception('PHP Sodium extension is not installed or enabled.');
        }

        // Generate Ed25519 keypair
        $keypair = sodium_crypto_sign_keypair();

        $publicKeyBytes = sodium_crypto_sign_publickey($keypair);
        $secretKeyBytes = sodium_crypto_sign_secretkey($keypair);

        // Solana public address is the Base58-encoded 32-byte public key
        $address = self::encode($publicKeyBytes);

        // Solana secret key is the Base58-encoded 64-byte secret key
        $privateKey = self::encode($secretKeyBytes);

        return [
            'address' => $address,
            'private_key' => $privateKey,
        ];
    }

    /**
     * Get the balance of a Solana address via JSON-RPC.
     *
     * @param string $address
     * @return float
     * @throws \Exception
     */
    public function getBalance(string $address): float
    {
        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(8)->post($rpcUrl, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'getBalance',
            'params' => [$address]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']['value'])) {
                return $data['result']['value'] / 1000000000;
            }
        }

        throw new \Exception(__('Failed to fetch balance from Solana RPC node.'));
    }

    /**
     * Fetch recent signatures for a Solana address.
     *
     * @param string $address
     * @param int $limit
     * @return array
     */
    public function getSignatures(string $address, int $limit = 20, ?string $before = null, ?string $until = null): array
    {
        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');

        $options = ['limit' => $limit];
        if ($before) {
            $options['before'] = $before;
        }
        if ($until) {
            $options['until'] = $until;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getSignaturesForAddress',
                'params' => [
                    $address,
                    $options
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result'])) {
                    return $data['result'];
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Solana RPC getSignaturesForAddress failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get transaction history with balance changes mapped (Incoming/Outgoing).
     *
     * @param string $address
     * @param int $limit
     * @return array
     */
    public function getTransactionsHistory(string $address, int $limit = 10, ?string $before = null, ?string $until = null): array
    {
        $signatures = $this->getSignatures($address, $limit, $before, $until);
        if (empty($signatures)) {
            return [];
        }

        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');
        $batchRequests = [];

        foreach ($signatures as $index => $sigInfo) {
            $batchRequests[] = [
                'jsonrpc' => '2.0',
                'id' => $index + 1,
                'method' => 'getTransaction',
                'params' => [
                    $sigInfo['signature'],
                    [
                        'encoding' => 'json',
                        'maxSupportedTransactionVersion' => 0
                    ]
                ]
            ];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->post($rpcUrl, $batchRequests);

            if ($response->successful()) {
                $batchResult = $response->json();

                $txList = [];
                foreach ($signatures as $index => $sigInfo) {
                    $signature = $sigInfo['signature'];
                    $txDetails = null;

                    if (is_array($batchResult)) {
                        foreach ($batchResult as $res) {
                            if (isset($res['id']) && $res['id'] == ($index + 1) && isset($res['result'])) {
                                $txDetails = $res['result'];
                                break;
                            }
                        }
                    }

                    $type = 'Other';
                    $amount = 0;
                    $fee = 0;
                    $tokenSymbol = 'SOL';

                    if ($txDetails) {
                        $meta = $txDetails['meta'] ?? null;
                        $transaction = $txDetails['transaction'] ?? null;

                        if ($meta && $transaction) {
                            if (isset($meta['fee'])) {
                                $fee = $meta['fee'] / 1000000000;
                            }

                            $accountKeys = [];
                            if (isset($transaction['message']['accountKeys'])) {
                                $accountKeys = $transaction['message']['accountKeys'];
                            }

                            // 1. Check for SPL token transfers first
                            $mintSymbols = [
                                'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v' => 'USDC',
                                'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB' => 'USDT'
                            ];

                            $preTokens = [];
                            if (isset($meta['preTokenBalances'])) {
                                foreach ($meta['preTokenBalances'] as $tb) {
                                    $owner = $tb['owner'] ?? null;
                                    if (!$owner && isset($tb['accountIndex']) && isset($accountKeys[$tb['accountIndex']])) {
                                        $ownerObj = $accountKeys[$tb['accountIndex']];
                                        $owner = is_array($ownerObj) ? ($ownerObj['pubkey'] ?? '') : $ownerObj;
                                    }
                                    if ($owner === $address) {
                                        $mint = $tb['mint'] ?? '';
                                        $preTokens[$mint] = (float) ($tb['uiTokenAmount']['uiAmount'] ?? 0);
                                    }
                                }
                            }

                            $postTokens = [];
                            if (isset($meta['postTokenBalances'])) {
                                foreach ($meta['postTokenBalances'] as $tb) {
                                    $owner = $tb['owner'] ?? null;
                                    if (!$owner && isset($tb['accountIndex']) && isset($accountKeys[$tb['accountIndex']])) {
                                        $ownerObj = $accountKeys[$tb['accountIndex']];
                                        $owner = is_array($ownerObj) ? ($ownerObj['pubkey'] ?? '') : $ownerObj;
                                    }
                                    if ($owner === $address) {
                                        $mint = $tb['mint'] ?? '';
                                        $postTokens[$mint] = (float) ($tb['uiTokenAmount']['uiAmount'] ?? 0);
                                    }
                                }
                            }

                            $allMints = array_unique(array_merge(array_keys($preTokens), array_keys($postTokens)));
                            $tokenTransferFound = false;
                            foreach ($allMints as $mint) {
                                $preVal = $preTokens[$mint] ?? 0.0;
                                $postVal = $postTokens[$mint] ?? 0.0;
                                $tDiff = $postVal - $preVal;
                                if (abs($tDiff) > 0.000001) {
                                    $symbol = $mintSymbols[$mint] ?? null;
                                    if (!$symbol) {
                                        $dbToken = \App\Models\BlockchainToken::where('address', $mint)->first();
                                        $symbol = $dbToken ? $dbToken->symbol : substr($mint, 0, 4) . '...';
                                    }
                                    if ($tDiff > 0) {
                                        $type = 'Incoming';
                                        $amount = $tDiff;
                                    } else {
                                        $type = 'Outgoing';
                                        $amount = abs($tDiff);
                                    }
                                    $tokenSymbol = $symbol;
                                    $tokenTransferFound = true;
                                    break;
                                }
                            }

                            // 2. Fallback to native SOL diff
                            if (!$tokenTransferFound) {
                                $addressIndex = -1;
                                foreach ($accountKeys as $i => $key) {
                                    $pubkey = is_array($key) ? ($key['pubkey'] ?? '') : $key;
                                    if ($pubkey === $address) {
                                        $addressIndex = $i;
                                        break;
                                    }
                                }

                                if ($addressIndex !== -1 && isset($meta['preBalances'][$addressIndex]) && isset($meta['postBalances'][$addressIndex])) {
                                    $pre = $meta['preBalances'][$addressIndex];
                                    $post = $meta['postBalances'][$addressIndex];
                                    $diff = $post - $pre;

                                    if ($diff > 0) {
                                        $type = 'Incoming';
                                        $amount = $diff / 1000000000;
                                    } elseif ($diff < 0) {
                                        $lamportsDiff = abs($diff);
                                        $feeLamports = (int) ($fee * 1000000000);
                                        if ($lamportsDiff <= $feeLamports + 100) {
                                            $type = 'Contract Interaction';
                                            $amount = 0;
                                        } else {
                                            $type = 'Outgoing';
                                            $amount = ($lamportsDiff - $feeLamports) / 1000000000;
                                        }
                                    } else {
                                        $type = 'Contract Interaction';
                                        $amount = 0;
                                    }
                                }
                            }
                        }
                    }

                    $txList[] = [
                        'signature' => $signature,
                        'slot' => $sigInfo['slot'],
                        'block_time' => $sigInfo['blockTime'] ?? null,
                        'error' => $sigInfo['err'] ?? null,
                        'memo' => $sigInfo['memo'] ?? null,
                        'type' => $type,
                        'amount' => $amount,
                        'fee' => $fee,
                        'token_symbol' => $tokenSymbol,
                    ];
                }

                return $txList;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Solana RPC batch getTransaction failed: ' . $e->getMessage());
        }

        $fallbackList = [];
        foreach ($signatures as $sigInfo) {
            $fallbackList[] = [
                'signature' => $sigInfo['signature'],
                'slot' => $sigInfo['slot'],
                'block_time' => $sigInfo['blockTime'] ?? null,
                'error' => $sigInfo['err'] ?? null,
                'memo' => $sigInfo['memo'] ?? null,
                'type' => 'Unknown',
                'amount' => 0,
                'fee' => 0,
                'token_symbol' => 'SOL',
            ];
        }
        return $fallbackList;
    }

    /**
     * Fetch USDC and USDT token balances for a Solana address.
     *
     * @param string $address
     * @return array
     */
    public function getSPLTokenBalances(string $address): array
    {
        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');
        $isDevnet = (strpos($rpcUrl, 'devnet') !== false);

        $usdcMint = $isDevnet
            ? 'Gh9ZwEmdLJ8DscKNTkTqPbNwLNNBjuSzaG9Vp2KGtKJr'
            : 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v';

        $usdtMint = $isDevnet
            ? 'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB'
            : 'Es9vMFrzaCERmJfrF4H2FYD4KCoNkY11McCe8BenwNYB';

        $balances = [
            'USDC' => 0.0,
            'USDT' => 0.0
        ];

        // Fetch USDC sequentially
        try {
            $responseUSDC = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getTokenAccountsByOwner',
                'params' => [
                    $address,
                    ['mint' => $usdcMint],
                    ['encoding' => 'jsonParsed']
                ]
            ]);

            if ($responseUSDC->successful()) {
                $data = $responseUSDC->json();
                if (isset($data['result']['value']) && is_array($data['result']['value'])) {
                    $uiAmount = 0.0;
                    foreach ($data['result']['value'] as $accountInfo) {
                        if (isset($accountInfo['account']['data']['parsed']['info']['tokenAmount']['uiAmount'])) {
                            $uiAmount += $accountInfo['account']['data']['parsed']['info']['tokenAmount']['uiAmount'];
                        }
                    }
                    $balances['USDC'] = $uiAmount;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Solana RPC getTokenAccountsByOwner USDC failed: ' . $e->getMessage());
        }

        // Fetch USDT sequentially
        try {
            $responseUSDT = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 2,
                'method' => 'getTokenAccountsByOwner',
                'params' => [
                    $address,
                    ['mint' => $usdtMint],
                    ['encoding' => 'jsonParsed']
                ]
            ]);

            if ($responseUSDT->successful()) {
                $data = $responseUSDT->json();
                if (isset($data['result']['value']) && is_array($data['result']['value'])) {
                    $uiAmount = 0.0;
                    foreach ($data['result']['value'] as $accountInfo) {
                        if (isset($accountInfo['account']['data']['parsed']['info']['tokenAmount']['uiAmount'])) {
                            $uiAmount += $accountInfo['account']['data']['parsed']['info']['tokenAmount']['uiAmount'];
                        }
                    }
                    $balances['USDT'] = $uiAmount;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Solana RPC getTokenAccountsByOwner USDT failed: ' . $e->getMessage());
        }

        return $balances;
    }

    /**
     * Fetch the token account public key (ATA) for a given owner address and token mint.
     *
     * @param string $ownerAddress
     * @param string $tokenMint
     * @return string|null
     */
    public function getSPLTokenAccount(string $ownerAddress, string $tokenMint): ?string
    {
        $rpcUrl = getSetting('solana_rpc_url', 'https://api.mainnet-beta.solana.com');
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'getTokenAccountsByOwner',
                'params' => [
                    $ownerAddress,
                    ['mint' => $tokenMint],
                    ['encoding' => 'jsonParsed']
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']['value']) && is_array($data['result']['value']) && count($data['result']['value']) > 0) {
                    return $data['result']['value'][0]['pubkey'] ?? null;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Solana RPC getTokenAccountsByOwner failed: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Check if a 32-byte public key is a valid point on the Ed25519 curve.
     *
     * @param string $bytes 32-byte binary string
     * @return bool
     */
    public static function isOnCurve(string $bytes): bool
    {
        $yBytes = $bytes;
        $yBytes[31] = chr(ord($yBytes[31]) & 0x7f);

        $y = gmp_init('0', 10);
        for ($i = 0; $i < 32; $i++) {
            $byteVal = gmp_init(ord($yBytes[$i]), 10);
            $shift = gmp_mul($byteVal, gmp_pow(gmp_init(2, 10), $i * 8));
            $y = gmp_add($y, $shift);
        }

        $p = gmp_sub(gmp_pow(gmp_init(2, 10), 255), gmp_init(19, 10));

        if (gmp_cmp($y, $p) >= 0) {
            return false;
        }

        $num = gmp_mod(gmp_sub($p, gmp_init(121665, 10)), $p);
        $den = gmp_init(121666, 10);
        $denInv = gmp_powm($den, gmp_sub($p, gmp_init(2, 10)), $p);
        $d = gmp_mod(gmp_mul($num, $denInv), $p);

        $y2 = gmp_mod(gmp_mul($y, $y), $p);
        $u = gmp_mod(gmp_sub($y2, gmp_init(1, 10)), $p);
        $v = gmp_mod(gmp_add(gmp_mul($d, $y2), gmp_init(1, 10)), $p);

        $vInv = gmp_powm($v, gmp_sub($p, gmp_init(2, 10)), $p);
        $x2 = gmp_mod(gmp_mul($u, $vInv), $p);

        if (gmp_cmp($x2, gmp_init(0, 10)) === 0) {
            return true;
        }

        $exponent = gmp_div(gmp_sub($p, gmp_init(1, 10)), gmp_init(2, 10));
        $rootCheck = gmp_powm($x2, $exponent, $p);

        return gmp_cmp($rootCheck, gmp_init(1, 10)) === 0;
    }

    /**
     * Find a program derived address (PDA) for seeds and program.
     */
    public static function findProgramAddress(array $seeds, string $programIdBytes): string
    {
        for ($bump = 255; $bump >= 0; $bump--) {
            $buffer = '';
            foreach ($seeds as $seed) {
                $buffer .= $seed;
            }
            $buffer .= chr($bump);
            $buffer .= $programIdBytes;
            $buffer .= "ProgramDerivedAddress";

            $hashBytes = hash('sha256', $buffer, true);

            if (!self::isOnCurve($hashBytes)) {
                return SolanaBase58::encode($hashBytes);
            }
        }
        throw new \Exception("Could not find a valid program derived address.");
    }

    /**
     * Derive Associated Token Address (ATA) for a given owner address and token mint.
     *
     * @param string $ownerAddressB58
     * @param string $tokenMintB58
     * @return string
     */
    public function deriveAssociatedTokenAddress(string $ownerAddressB58, string $tokenMintB58): string
    {
        $walletBytes = SolanaBase58::decode($ownerAddressB58);
        $mintBytes = SolanaBase58::decode($tokenMintB58);

        $tokenProgramBytes = SolanaBase58::decode('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');
        $associatedTokenProgramBytes = SolanaBase58::decode('ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJA8knL');

        $seeds = [
            $walletBytes,
            $tokenProgramBytes,
            $mintBytes
        ];

        return self::findProgramAddress($seeds, $associatedTokenProgramBytes);
    }
}
