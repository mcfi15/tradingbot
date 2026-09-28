<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Builds, signs, and broadcasts TRON blockchain transactions.
 * 
 * Uses secp256k1 curve points arithmetic (via GMP) and RFC 6979 deterministic k
 * to sign the 32-byte SHA-256 hash (txID) computed by the TRON node.
 */
class TronTransactionSigner
{
    // secp256k1 curve parameters
    const N_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const P_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    const GX_HEX = '79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    const GY_HEX = '483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    /**
     * Build a native TRX transfer transaction on the node.
     */
    public static function buildTrxTransfer(string $rpcUrl, string $from, string $to, int $amountSun): ?array
    {
        try {
            $response = Http::withoutVerifying()->timeout(12)->post(rtrim($rpcUrl, '/') . '/wallet/createtransaction', [
                'owner_address' => $from,
                'to_address' => $to,
                'amount' => $amountSun,
                'visible' => true
            ]);

            if ($response->successful()) {
                $tx = $response->json();
                if (isset($tx['txID'])) {
                    return $tx;
                }
                Log::error("Tron buildTrxTransfer failed: " . json_encode($tx));
            }
        } catch (\Exception $e) {
            Log::error("Exception in Tron buildTrxTransfer: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Build a TRC-20 token transfer transaction on the node.
     */
    public static function buildTrc20Transfer(
        string $rpcUrl,
        string $from,
        string $to,
        string $contractAddress,
        string $amountRaw,
        int $feeLimit = 40000000 // 40 TRX fee limit
    ): ?array {
        try {
            $tronService = new TronWalletGeneratorService();
            
            // Get 20-byte address payload for the recipient
            $destHex = $tronService->tronAddressToHex($to);
            $paddedDest = str_pad(substr($destHex, 2), 64, '0', STR_PAD_LEFT);
            
            // Get hex representation of the amount in big-endian uint256
            $gmpAmount = gmp_init($amountRaw, 10);
            $amountHex = str_pad(gmp_strval($gmpAmount, 16), 64, '0', STR_PAD_LEFT);
            
            $parameter = $paddedDest . $amountHex;

            $response = Http::withoutVerifying()->timeout(12)->post(rtrim($rpcUrl, '/') . '/wallet/triggersmartcontract', [
                'owner_address' => $from,
                'contract_address' => $contractAddress,
                'function_selector' => 'transfer(address,uint256)',
                'parameter' => $parameter,
                'fee_limit' => $feeLimit,
                'visible' => true
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']['result']) && $data['result']['result'] === true && isset($data['transaction'])) {
                    return $data['transaction'];
                }
                Log::error("Tron buildTrc20Transfer failed: " . json_encode($data));
            }
        } catch (\Exception $e) {
            Log::error("Exception in Tron buildTrc20Transfer: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Sign the transaction hash (txID) using the private key and append to signature array.
     */
    public static function signTransaction(array $transaction, string $privateKeyHex): ?array
    {
        $txId = $transaction['txID'] ?? null;
        if (!$txId) {
            Log::error("Tron signTransaction: txID is missing.");
            return null;
        }

        try {
            $signatureHex = self::signHash($txId, $privateKeyHex);
            
            if (!isset($transaction['signature'])) {
                $transaction['signature'] = [];
            }
            $transaction['signature'][] = $signatureHex;
            
            return $transaction;
        } catch (\Exception $e) {
            Log::error("Tron signTransaction exception: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Broadcast the signed transaction JSON to the network.
     */
    public static function broadcastTransaction(string $rpcUrl, array $signedTransaction): ?string
    {
        try {
            $response = Http::withoutVerifying()->timeout(15)->post(rtrim($rpcUrl, '/') . '/wallet/broadcasttransaction', $signedTransaction);
            if ($response->successful()) {
                $res = $response->json();
                if (isset($res['result']) && $res['result'] === true) {
                    return $signedTransaction['txID'];
                }
                Log::error("Tron broadcastTransaction failed: " . json_encode($res));
            }
        } catch (\Exception $e) {
            Log::error("Exception in Tron broadcastTransaction: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Cryptographically sign a 32-byte hash using secp256k1 and RFC 6979.
     */
    private static function signHash(string $hashHex, string $privateKeyHex): string
    {
        $hashHex = ltrim($hashHex, '0x');
        $privHex = ltrim($privateKeyHex, '0x');

        $n  = gmp_init(self::N_HEX, 16);
        $p  = gmp_init(self::P_HEX, 16);
        $Gx = gmp_init(self::GX_HEX, 16);
        $Gy = gmp_init(self::GY_HEX, 16);
        
        $d  = gmp_init($privHex, 16);
        $z  = gmp_init($hashHex, 16);

        $curve = new EthereumWalletGeneratorService();
        $k = self::rfc6979($hashHex, $privHex, $n);

        // R = k · G
        [$Rx, $Ry] = $curve->pointMultiply($k, [$Gx, $Gy], $p);

        // r = Rx mod n
        $r = gmp_mod($Rx, $n);

        // s = k⁻¹ · (z + r·d) mod n
        $s = gmp_mod(gmp_mul(gmp_invert($k, $n), gmp_add($z, gmp_mul($r, $d))), $n);

        $recoveryId = (gmp_cmp(gmp_mod($Ry, gmp_init(2)), gmp_init(1)) === 0) ? 1 : 0;

        // BIP-62 low-S normalisation
        if (gmp_cmp($s, gmp_div_q($n, 2)) > 0) {
            $s = gmp_sub($n, $s);
            $recoveryId = 1 - $recoveryId;
        }

        $rBytes = hex2bin(str_pad(gmp_strval($r, 16), 64, '0', STR_PAD_LEFT));
        $sBytes = hex2bin(str_pad(gmp_strval($s, 16), 64, '0', STR_PAD_LEFT));
        $vByte  = chr($recoveryId);

        return bin2hex($rBytes . $sBytes . $vByte);
    }

    /**
     * RFC 6979 deterministic k generation.
     */
    private static function rfc6979(string $hashHex, string $privHex, \GMP $n): \GMP
    {
        $h = hex2bin(str_pad($hashHex, 64, '0', STR_PAD_LEFT));
        $d = hex2bin(str_pad($privHex, 64, '0', STR_PAD_LEFT));

        $V = str_repeat("\x01", 32);
        $K = str_repeat("\x00", 32);

        $K = hash_hmac('sha256', $V . "\x00" . $d . $h, $K, true);
        $V = hash_hmac('sha256', $V, $K, true);
        $K = hash_hmac('sha256', $V . "\x01" . $d . $h, $K, true);
        $V = hash_hmac('sha256', $V, $K, true);

        while (true) {
            $T = '';
            while (strlen($T) < 32) {
                $V = hash_hmac('sha256', $V, $K, true);
                $T .= $V;
            }

            $k = gmp_init(bin2hex(substr($T, 0, 32)), 16);
            if (gmp_cmp($k, 1) >= 0 && gmp_cmp($k, $n) < 0) {
                return $k;
            }

            $K = hash_hmac('sha256', $V . "\x00", $K, true);
            $V = hash_hmac('sha256', $V, $K, true);
        }
    }
}
