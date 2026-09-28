<?php

namespace App\Services;

/**
 * Builds and signs raw Ethereum transactions (legacy EIP-155 format).
 *
 * Supports:
 *  - Native ETH transfers
 *  - ERC-20 token transfers (e.g. USDC, USDT)
 *
 * Uses Secp256k1 ECDSA with RFC 6979 deterministic k-value generation.
 * Reuses EthereumWalletGeneratorService for Keccak-256 and curve point multiplication.
 *
 * Requirements: PHP GMP extension.
 */
class EthereumTransactionSigner
{
    // Secp256k1 curve parameters (same as EthereumWalletGeneratorService)
    const N_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const P_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    const GX_HEX = '79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    const GY_HEX = '483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    // ─── Public Transaction Builders ─────────────────────────────────────────

    /**
     * Build and sign a native ETH transfer transaction (EIP-155 replay-protected).
     *
     * @param string $privateKeyHex  32-byte private key as hex (with or without '0x')
     * @param string $toAddress      Recipient address (checksummed or lowercase)
     * @param string $valueWei       Amount to send in wei as a decimal string (e.g. '1000000000000000000' = 1 ETH)
     * @param int    $nonce          Account nonce from eth_getTransactionCount
     * @param string $gasPriceWei    Gas price in wei as a decimal string
     * @param int    $gasLimit       Gas limit (21000 for a plain ETH transfer)
     * @param int    $chainId        EIP-155 chain ID (1 = mainnet, 11155111 = Sepolia)
     * @return string '0x'-prefixed signed raw transaction hex, ready for eth_sendRawTransaction
     */
    public static function buildAndSignEthTransfer(
        string $privateKeyHex,
        string $toAddress,
        string $valueWei,
        int    $nonce,
        string $gasPriceWei,
        int    $gasLimit,
        int    $chainId = 1
    ): string {
        $privHex = ltrim($privateKeyHex, '0x');
        $toBytes = hex2bin(str_pad(strtolower(ltrim($toAddress, '0x')), 40, '0', STR_PAD_LEFT));

        // EIP-155 pre-image: [nonce, gasPrice, gasLimit, to, value, data, chainId, 0, 0]
        $presign = self::rlpList([
            self::intBytes($nonce),
            self::weiBytes($gasPriceWei),
            self::intBytes($gasLimit),
            $toBytes,
            self::weiBytes($valueWei),
            '',                         // empty data for plain ETH transfer
            self::intBytes($chainId),
            '',                         // v = 0 placeholder
            '',                         // r = 0 placeholder
        ]);

        $keccak = new EthereumWalletGeneratorService();
        $hash   = $keccak->keccak256($presign);

        [$rBytes, $sBytes, $v] = self::ecdsaSign($hash, $privHex, $chainId);

        $signed = self::rlpList([
            self::intBytes($nonce),
            self::weiBytes($gasPriceWei),
            self::intBytes($gasLimit),
            $toBytes,
            self::weiBytes($valueWei),
            '',
            self::intBytes($v),
            $rBytes,
            $sBytes,
        ]);

        return '0x' . bin2hex($signed);
    }

    /**
     * Build and sign an ERC-20 token transfer transaction (EIP-155 replay-protected).
     *
     * Encodes the `transfer(address,uint256)` ABI call and sends it to the contract.
     *
     * @param string $privateKeyHex   32-byte private key hex (with or without '0x')
     * @param string $toAddress       Recipient wallet address
     * @param string $contractAddress ERC-20 token contract address
     * @param string $amountRaw       Token amount in smallest units as a decimal string (e.g. '1000000' = 1 USDC)
     * @param int    $nonce           Account nonce
     * @param string $gasPriceWei     Gas price in wei (decimal string)
     * @param int    $gasLimit        Gas limit (typically 65000 for ERC-20 transfer)
     * @param int    $chainId         EIP-155 chain ID
     * @return string '0x'-prefixed signed raw transaction hex
     */
    public static function buildAndSignErc20Transfer(
        string $privateKeyHex,
        string $toAddress,
        string $contractAddress,
        string $amountRaw,
        int    $nonce,
        string $gasPriceWei,
        int    $gasLimit,
        int    $chainId = 1
    ): string {
        $privHex      = ltrim($privateKeyHex, '0x');
        $contractBytes = hex2bin(str_pad(strtolower(ltrim($contractAddress, '0x')), 40, '0', STR_PAD_LEFT));
        $keccak       = new EthereumWalletGeneratorService();

        // Function selector: first 4 bytes of keccak256("transfer(address,uint256)")
        $selector   = hex2bin(substr($keccak->keccak256('transfer(address,uint256)'), 0, 8));

        // ABI-encode address (left-padded to 32 bytes)
        $encodedTo  = hex2bin(str_pad(strtolower(ltrim($toAddress, '0x')), 64, '0', STR_PAD_LEFT));

        // ABI-encode uint256 amount (left-padded to 32 bytes)
        $amountHex  = gmp_strval(gmp_init($amountRaw), 16);
        $encodedAmt = hex2bin(str_pad($amountHex, 64, '0', STR_PAD_LEFT));

        $callData = $selector . $encodedTo . $encodedAmt;

        $presign = self::rlpList([
            self::intBytes($nonce),
            self::weiBytes($gasPriceWei),
            self::intBytes($gasLimit),
            $contractBytes,             // 'to' = token contract, not the recipient
            '',                         // value = 0 (no ETH sent in ERC-20 transfer)
            $callData,
            self::intBytes($chainId),
            '',
            '',
        ]);

        $hash = $keccak->keccak256($presign);

        [$rBytes, $sBytes, $v] = self::ecdsaSign($hash, $privHex, $chainId);

        $signed = self::rlpList([
            self::intBytes($nonce),
            self::weiBytes($gasPriceWei),
            self::intBytes($gasLimit),
            $contractBytes,
            '',
            $callData,
            self::intBytes($v),
            $rBytes,
            $sBytes,
        ]);

        return '0x' . bin2hex($signed);
    }

    // ─── ECDSA Signing ────────────────────────────────────────────────────────

    /**
     * Sign a 32-byte message hash using Secp256k1 ECDSA with RFC 6979 deterministic k.
     *
     * @param string $hashHex    64-char hex string (32-byte hash to sign)
     * @param string $privKeyHex 64-char hex string (32-byte private key)
     * @param int    $chainId    EIP-155 chain ID for v-value calculation
     * @return array [string $r (32 bytes binary), string $s (32 bytes binary), int $v]
     */
    private static function ecdsaSign(string $hashHex, string $privKeyHex, int $chainId): array
    {
        $n  = gmp_init(self::N_HEX, 16);
        $p  = gmp_init(self::P_HEX, 16);
        $Gx = gmp_init(self::GX_HEX, 16);
        $Gy = gmp_init(self::GY_HEX, 16);
        $d  = gmp_init($privKeyHex, 16);
        $z  = gmp_init($hashHex, 16);

        $curve = new EthereumWalletGeneratorService();

        // Generate deterministic k per RFC 6979
        $k = self::rfc6979($hashHex, $privKeyHex, $n);

        // R = k · G (scalar multiplication on Secp256k1)
        [$Rx, $Ry] = $curve->pointMultiply($k, [$Gx, $Gy], $p);

        // r = Rx mod n
        $r = gmp_mod($Rx, $n);

        // s = k⁻¹ · (z + r·d) mod n
        $s = gmp_mod(gmp_mul(gmp_invert($k, $n), gmp_add($z, gmp_mul($r, $d))), $n);

        $recoveryId = (gmp_cmp(gmp_mod($Ry, gmp_init(2)), gmp_init(1)) === 0) ? 1 : 0;

        // BIP-62 low-S normalisation: if s > n/2, use n - s AND flip recoveryId parity
        if (gmp_cmp($s, gmp_div_q($n, 2)) > 0) {
            $s = gmp_sub($n, $s);
            $recoveryId = 1 - $recoveryId;
        }

        // EIP-155 replay-protected v: recoveryId + 35 + 2 * chainId
        $v          = $recoveryId + 35 + 2 * $chainId;

        // Encode r and s as 32-byte big-endian binary strings
        $rBytes = hex2bin(str_pad(gmp_strval($r, 16), 64, '0', STR_PAD_LEFT));
        $sBytes = hex2bin(str_pad(gmp_strval($s, 16), 64, '0', STR_PAD_LEFT));

        return [$rBytes, $sBytes, $v];
    }

    /**
     * Generate a deterministic signing nonce k using RFC 6979 (HMAC-DRBG with SHA-256).
     *
     * Guarantees 1 ≤ k < n and eliminates reliance on random number generation for signing.
     *
     * @param string $hashHex   64-char hex message hash
     * @param string $privHex   64-char hex private key
     * @param \GMP   $n         Curve order
     * @return \GMP
     */
    private static function rfc6979(string $hashHex, string $privHex, \GMP $n): \GMP
    {
        $h = hex2bin(str_pad($hashHex, 64, '0', STR_PAD_LEFT));  // 32 bytes
        $d = hex2bin(str_pad($privHex, 64, '0', STR_PAD_LEFT));  // 32 bytes

        $V = str_repeat("\x01", 32);
        $K = str_repeat("\x00", 32);

        // Step d
        $K = hash_hmac('sha256', $V . "\x00" . $d . $h, $K, true);
        // Step e
        $V = hash_hmac('sha256', $V, $K, true);
        // Step f
        $K = hash_hmac('sha256', $V . "\x01" . $d . $h, $K, true);
        // Step g
        $V = hash_hmac('sha256', $V, $K, true);

        // Step h: generate candidates until valid
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

            // Retry with updated K, V
            $K = hash_hmac('sha256', $V . "\x00", $K, true);
            $V = hash_hmac('sha256', $V, $K, true);
        }
    }

    // ─── RLP Encoding ────────────────────────────────────────────────────────

    /**
     * RLP-encode a list of binary strings as a list payload.
     */
    private static function rlpList(array $items): string
    {
        $payload = '';
        foreach ($items as $item) {
            $payload .= self::rlpItem($item);
        }

        $len = strlen($payload);
        if ($len <= 55) {
            return chr(0xc0 + $len) . $payload;
        }

        $lenBytes = self::minBytes($len);
        return chr(0xf7 + strlen($lenBytes)) . $lenBytes . $payload;
    }

    /**
     * RLP-encode a single binary string.
     */
    private static function rlpItem(string $data): string
    {
        $len = strlen($data);

        if ($len === 0) {
            return "\x80"; // empty string → 0x80
        }

        if ($len === 1 && ord($data[0]) < 0x80) {
            return $data; // single byte [0x00..0x7f] is its own encoding
        }

        if ($len <= 55) {
            return chr(0x80 + $len) . $data;
        }

        $lenBytes = self::minBytes($len);
        return chr(0xb7 + strlen($lenBytes)) . $lenBytes . $data;
    }

    // ─── Integer Encoding Helpers ────────────────────────────────────────────

    /**
     * Encode a PHP integer (nonce, gas limit, chain ID, v) as a minimal big-endian byte string.
     * Returns empty string for 0 (which RLP encodes as the empty-string token 0x80).
     */
    private static function intBytes(int $value): string
    {
        if ($value === 0) return '';
        $hex = dechex($value);
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
        return hex2bin($hex);
    }

    /**
     * Encode a large integer given as a decimal string (wei values) as a minimal big-endian byte string.
     * Returns empty string for 0.
     */
    private static function weiBytes(string $decimal): string
    {
        $gmp = gmp_init($decimal);
        if (gmp_cmp($gmp, 0) === 0) return '';
        $hex = gmp_strval($gmp, 16);
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
        return hex2bin($hex);
    }

    /**
     * Encode a positive integer as a minimal-length big-endian byte string (used for RLP length prefixes).
     */
    private static function minBytes(int $value): string
    {
        if ($value === 0) return '';
        $hex = dechex($value);
        if (strlen($hex) % 2 !== 0) $hex = '0' . $hex;
        return hex2bin($hex);
    }
}
