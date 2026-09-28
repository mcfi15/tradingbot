<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Ethereum wallet generation, address derivation, and balance/nonce RPC queries.
 *
 * Uses:
 *  - PHP GMP extension for arbitrary-precision Secp256k1 curve arithmetic.
 *  - A pure-PHP Keccak-256 implementation (Ethereum's hash variant, NOT NIST SHA3-256).
 *
 * Requirements: PHP GMP extension, 64-bit PHP build (PHP_INT_SIZE === 8).
 */
class EthereumWalletGeneratorService
{
    // ─── Secp256k1 Curve Parameters ──────────────────────────────────────────
    const P_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    const N_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const GX_HEX = '79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    const GY_HEX = '483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    // ─── Wallet Generation ────────────────────────────────────────────────────

    /**
     * Generate a new Ethereum wallet.
     *
     * @return array ['address' => '0x...', 'private_key' => '0x...']
     * @throws \Exception
     */
    public function generateWallet(): array
    {
        if (!extension_loaded('gmp')) {
            throw new \Exception('PHP GMP extension is required for Ethereum wallet generation.');
        }
        if (PHP_INT_SIZE < 8) {
            throw new \Exception('64-bit PHP build is required (for Keccak-256).');
        }

        // Generate random private key until it falls within the valid Secp256k1 range [1, n-1]
        $n = gmp_init(self::N_HEX, 16);
        do {
            $privBytes   = random_bytes(32);
            $privKeyHex  = bin2hex($privBytes);
            $privKey     = gmp_init($privKeyHex, 16);
        } while (gmp_cmp($privKey, 1) < 0 || gmp_cmp($privKey, $n) >= 0);

        $address = $this->privateKeyToAddress($privKeyHex);

        return [
            'address'     => $address,
            'private_key' => '0x' . $privKeyHex,
        ];
    }

    /**
     * Derive the EIP-55 checksummed Ethereum address from a 32-byte private key hex string.
     */
    public function privateKeyToAddress(string $privateKeyHex): string
    {
        $privHex = ltrim($privateKeyHex, '0x');
        $pubHex  = $this->privateKeyToPublicKey($privHex);
        $hash    = $this->keccak256(hex2bin($pubHex));
        return $this->toChecksumAddress('0x' . substr($hash, -40));
    }

    /**
     * Compute the 64-byte uncompressed public key (128 hex chars, no 0x04 prefix) from a private key.
     */
    public function privateKeyToPublicKey(string $privateKeyHex): string
    {
        $p  = gmp_init(self::P_HEX, 16);
        $Gx = gmp_init(self::GX_HEX, 16);
        $Gy = gmp_init(self::GY_HEX, 16);
        $k  = gmp_init($privateKeyHex, 16);

        [$x, $y] = $this->pointMultiply($k, [$Gx, $Gy], $p);

        return str_pad(gmp_strval($x, 16), 64, '0', STR_PAD_LEFT)
             . str_pad(gmp_strval($y, 16), 64, '0', STR_PAD_LEFT);
    }

    /**
     * Check that a string is a valid Ethereum address (40 hex chars, optionally 0x-prefixed).
     */
    public static function isValidAddress(string $address): bool
    {
        $hex = ltrim($address, '0x');
        return strlen($hex) === 40 && ctype_xdigit($hex);
    }

    /**
     * Apply EIP-55 mixed-case checksum encoding to an Ethereum address.
     */
    public function toChecksumAddress(string $address): string
    {
        $addr   = strtolower(ltrim($address, '0x'));
        $hash   = $this->keccak256($addr);
        $result = '0x';
        for ($i = 0; $i < 40; $i++) {
            $result .= hexdec($hash[$i]) >= 8 ? strtoupper($addr[$i]) : $addr[$i];
        }
        return $result;
    }

    // ─── RPC Queries ──────────────────────────────────────────────────────────

    /**
     * Fetch the native ETH balance of an address in ETH (not wei).
     *
     * @throws \Exception on RPC failure
     */
    public function getEthBalance(string $address, string $rpcUrl): float
    {
        $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
            'jsonrpc' => '2.0', 'id' => 1,
            'method'  => 'eth_getBalance',
            'params'  => [$address, 'latest'],
        ]);

        if ($response->successful()) {
            $hex = ltrim($response->json('result') ?? '0x0', '0x');
            $wei = gmp_init($hex ?: '0', 16);
            return (float) gmp_strval($wei) / 1e18;
        }

        throw new \Exception('Failed to fetch ETH balance from Ethereum RPC.');
    }

    /**
     * Fetch the ERC-20 token balance of an address in raw token units (as a GMP integer).
     *
     * @param string $tokenAddress  ERC-20 contract address
     */
    public function getErc20BalanceRaw(string $walletAddress, string $tokenAddress, string $rpcUrl): \GMP
    {
        $selector    = substr($this->keccak256('balanceOf(address)'), 0, 8);
        $paddedOwner = str_pad(ltrim($walletAddress, '0x'), 64, '0', STR_PAD_LEFT);
        $callData    = '0x' . $selector . $paddedOwner;

        $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
            'jsonrpc' => '2.0', 'id' => 1,
            'method'  => 'eth_call',
            'params'  => [['to' => $tokenAddress, 'data' => $callData], 'latest'],
        ]);

        if ($response->successful()) {
            $hex = ltrim($response->json('result') ?? '0x0', '0x');
            return gmp_init($hex ?: '0', 16);
        }

        return gmp_init(0);
    }

    /**
     * Get the current nonce (transaction count) for an address.
     *
     * @throws \Exception on RPC failure
     */
    public function getNonce(string $address, string $rpcUrl): int
    {
        $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
            'jsonrpc' => '2.0', 'id' => 1,
            'method'  => 'eth_getTransactionCount',
            'params'  => [$address, 'latest'],
        ]);

        if ($response->successful()) {
            return hexdec(ltrim($response->json('result') ?? '0x0', '0x'));
        }

        throw new \Exception('Failed to fetch nonce from Ethereum RPC.');
    }

    /**
     * Get the current gas price in wei (returned as a decimal string).
     *
     * @throws \Exception on RPC failure
     */
    public function getGasPrice(string $rpcUrl): string
    {
        $response = Http::withoutVerifying()->timeout(10)->post($rpcUrl, [
            'jsonrpc' => '2.0', 'id' => 1,
            'method'  => 'eth_gasPrice',
            'params'  => [],
        ]);

        if ($response->successful()) {
            $hex = ltrim($response->json('result') ?? '0x0', '0x');
            return gmp_strval(gmp_init($hex ?: '0', 16));
        }

        throw new \Exception('Failed to fetch gas price from Ethereum RPC.');
    }

    // ─── Secp256k1 Curve Arithmetic ───────────────────────────────────────────

    /**
     * Double-and-add scalar multiplication: result = k * point (mod p).
     * Public so EthereumTransactionSigner can reuse this for ECDSA.
     *
     * @param \GMP  $k     Scalar multiplier
     * @param array $point [\GMP $x, \GMP $y]
     * @param \GMP  $p     Field prime
     * @return array [\GMP $x, \GMP $y]
     */
    public function pointMultiply(\GMP $k, array $point, \GMP $p): array
    {
        $result = null; // Point at infinity
        $addend = $point;

        while (gmp_cmp($k, 0) > 0) {
            if (gmp_testbit($k, 0)) {
                $result = ($result === null) ? $addend : $this->pointAdd($result, $addend, $p);
            }
            $addend = $this->pointDouble($addend, $p);
            $k      = gmp_div_q($k, 2);
        }

        return $result ?? [gmp_init(0), gmp_init(0)];
    }

    /**
     * Elliptic curve affine point addition (P + Q).
     */
    public function pointAdd(array $P, array $Q, \GMP $p): array
    {
        [$Px, $Py] = $P;
        [$Qx, $Qy] = $Q;

        // λ = (Qy - Py) · (Qx - Px)^{-1} mod p
        $lambda = gmp_mod(
            gmp_mul(gmp_sub($Qy, $Py), gmp_invert(gmp_sub($Qx, $Px), $p)),
            $p
        );

        $Rx = gmp_mod(gmp_sub(gmp_sub(gmp_mul($lambda, $lambda), $Px), $Qx), $p);
        $Ry = gmp_mod(gmp_sub(gmp_mul($lambda, gmp_sub($Px, $Rx)), $Py), $p);

        if (gmp_cmp($Rx, 0) < 0) $Rx = gmp_add($Rx, $p);
        if (gmp_cmp($Ry, 0) < 0) $Ry = gmp_add($Ry, $p);

        return [$Rx, $Ry];
    }

    /**
     * Elliptic curve affine point doubling (P + P).
     */
    public function pointDouble(array $P, \GMP $p): array
    {
        [$Px, $Py] = $P;

        // λ = 3·Px² · (2·Py)^{-1} mod p
        $lambda = gmp_mod(
            gmp_mul(
                gmp_mul(gmp_init(3), gmp_mul($Px, $Px)),
                gmp_invert(gmp_mul(gmp_init(2), $Py), $p)
            ),
            $p
        );

        $Rx = gmp_mod(gmp_sub(gmp_mul($lambda, $lambda), gmp_mul(gmp_init(2), $Px)), $p);
        $Ry = gmp_mod(gmp_sub(gmp_mul($lambda, gmp_sub($Px, $Rx)), $Py), $p);

        if (gmp_cmp($Rx, 0) < 0) $Rx = gmp_add($Rx, $p);
        if (gmp_cmp($Ry, 0) < 0) $Ry = gmp_add($Ry, $p);

        return [$Rx, $Ry];
    }

    // ─── Keccak-256 ──────────────────────────────────────────────────────────

    /**
     * Compute Keccak-256 of binary input data.
     * Returns a 64-character lowercase hex string.
     *
     * This is Ethereum's hash primitive (padding byte 0x01) and is NOT identical
     * to NIST SHA3-256 (which uses padding byte 0x06).
     */
    public function keccak256(string $data): string
    {
        $rate      = 136; // bytes (1088-bit rate for 256-bit output capacity)
        $outputLen = 32;  // bytes

        // Multi-rate padding: 0x01 ... 0x80
        $padLen = $rate - (strlen($data) % $rate);
        if ($padLen === 1) {
            $data .= "\x81";                                          // 0x01|0x80 combined
        } else {
            $data .= "\x01" . str_repeat("\x00", $padLen - 2) . "\x80";
        }

        // Initialize 25-lane state (each lane = 64-bit PHP signed integer)
        $state = array_fill(0, 25, 0);

        // Absorb: XOR each $rate-byte block into the state, then permute
        $numBlocks = strlen($data) / $rate;
        for ($b = 0; $b < $numBlocks; $b++) {
            $block = substr($data, $b * $rate, $rate);
            for ($i = 0; $i < $rate / 8; $i++) {
                // 'P' = unsigned 64-bit little-endian; on 64-bit PHP stored as signed int (correct bit pattern)
                $state[$i] ^= unpack('P', substr($block, $i * 8, 8))[1];
            }
            $state = $this->keccakF1600($state);
        }

        // Squeeze: take first $outputLen bytes from state lanes
        $output = '';
        for ($i = 0; $i < $outputLen / 8; $i++) {
            $output .= pack('P', $state[$i]);
        }

        return bin2hex($output);
    }

    /**
     * Keccak-f[1600] permutation: 24 rounds of θ ρ π χ ι.
     *
     * @param int[] $state  25-element array of 64-bit lanes (PHP signed integers)
     * @return int[]
     */
    private function keccakF1600(array $state): array
    {
        // Round constants — values with bit 63 set are expressed using PHP_INT_MIN bitmask
        // so they remain native 64-bit integers rather than overflowing to float.
        static $RC = null;
        if ($RC === null) {
            $M  = PHP_INT_MIN; // 0x8000000000000000 as PHP signed int
            $RC = [
                0x0000000000000001,        // RC[ 0]
                0x0000000000008082,        // RC[ 1]
                $M | 0x000000000000808A,   // RC[ 2] = 0x800000000000808A
                $M | 0x0000000080008000,   // RC[ 3] = 0x8000000080008000
                0x000000000000808B,        // RC[ 4]
                0x0000000080000001,        // RC[ 5]
                $M | 0x0000000080008081,   // RC[ 6] = 0x8000000080008081
                $M | 0x0000000000008009,   // RC[ 7] = 0x8000000000008009
                0x000000000000008A,        // RC[ 8]
                0x0000000000000088,        // RC[ 9]
                0x0000000080008009,        // RC[10]
                0x000000008000000A,        // RC[11]
                0x000000008000808B,        // RC[12]
                $M | 0x000000000000008B,   // RC[13] = 0x800000000000008B
                $M | 0x0000000000008089,   // RC[14] = 0x8000000000008089
                $M | 0x0000000000008003,   // RC[15] = 0x8000000000008003
                $M | 0x0000000000008002,   // RC[16] = 0x8000000000008002
                $M | 0x0000000000000080,   // RC[17] = 0x8000000000000080
                0x000000000000800A,        // RC[18]
                $M | 0x000000008000000A,   // RC[19] = 0x800000008000000A
                $M | 0x0000000080008081,   // RC[20] = 0x8000000080008081
                $M | 0x0000000000008080,   // RC[21] = 0x8000000000008080
                0x0000000080000001,        // RC[22]
                $M | 0x0000000080008008,   // RC[23] = 0x8000000080008008
            ];
        }

        // Rotation offsets indexed by [x][y] per the Keccak spec (NIST FIPS 202, Table 2)
        static $ROT = [
            [ 0, 36,  3, 41, 18], // x=0
            [ 1, 44, 10, 45,  2], // x=1
            [62,  6, 43, 15, 61], // x=2
            [28, 55, 25, 21, 56], // x=3
            [27, 20, 39,  8, 14], // x=4
        ];

        for ($round = 0; $round < 24; $round++) {

            // ── θ (Theta) ──────────────────────────────────────────────────
            $C = [
                $state[0]  ^ $state[5]  ^ $state[10] ^ $state[15] ^ $state[20],
                $state[1]  ^ $state[6]  ^ $state[11] ^ $state[16] ^ $state[21],
                $state[2]  ^ $state[7]  ^ $state[12] ^ $state[17] ^ $state[22],
                $state[3]  ^ $state[8]  ^ $state[13] ^ $state[18] ^ $state[23],
                $state[4]  ^ $state[9]  ^ $state[14] ^ $state[19] ^ $state[24],
            ];
            $D = [
                $C[4] ^ $this->rotl64($C[1], 1),
                $C[0] ^ $this->rotl64($C[2], 1),
                $C[1] ^ $this->rotl64($C[3], 1),
                $C[2] ^ $this->rotl64($C[4], 1),
                $C[3] ^ $this->rotl64($C[0], 1),
            ];
            for ($i = 0; $i < 25; $i++) {
                $state[$i] ^= $D[$i % 5];
            }

            // ── ρ + π (Rho + Pi combined) ──────────────────────────────────
            // B[y_old + 5*((2x+3y)%5)] = ROTL(state[x + 5y], ROT[x][y])
            $B = array_fill(0, 25, 0);
            for ($x = 0; $x < 5; $x++) {
                for ($y = 0; $y < 5; $y++) {
                    $B[$y + 5 * ((2 * $x + 3 * $y) % 5)] =
                        $this->rotl64($state[$x + 5 * $y], $ROT[$x][$y]);
                }
            }

            // ── χ (Chi) ────────────────────────────────────────────────────
            for ($y = 0; $y < 5; $y++) {
                $o = $y * 5;
                $state[$o + 0] = $B[$o + 0] ^ ((~$B[$o + 1]) & $B[$o + 2]);
                $state[$o + 1] = $B[$o + 1] ^ ((~$B[$o + 2]) & $B[$o + 3]);
                $state[$o + 2] = $B[$o + 2] ^ ((~$B[$o + 3]) & $B[$o + 4]);
                $state[$o + 3] = $B[$o + 3] ^ ((~$B[$o + 4]) & $B[$o + 0]);
                $state[$o + 4] = $B[$o + 4] ^ ((~$B[$o + 0]) & $B[$o + 1]);
            }

            // ── ι (Iota) ───────────────────────────────────────────────────
            $state[0] ^= $RC[$round];
        }

        return $state;
    }

    /**
     * Rotate a 64-bit value left by n positions.
     *
     * PHP integers are signed 64-bit. >> is arithmetic (sign-extending), so we
     * must mask out the sign-extended bits when emulating a logical right shift.
     *
     * For n in [1..63]:
     *   rotl64(x, n) = (x << n) | logicalRightShift(x, 64 - n)
     *   logicalRightShift(x, k) = (x >> k) & ~((-1) << (64-k))
     *                           = (x >> k) & ~((-1) << n)
     */
    private function rotl64(int $x, int $n): int
    {
        if ($n === 0) return $x;
        // (-1) << n  gives (64-n) high bits set, n low bits clear.
        // ~((-1) << n) gives n low bits set (exact mask for the wrapped bits).
        return ($x << $n) | (($x >> (64 - $n)) & ~((-1) << $n));
    }
}
