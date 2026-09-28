<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Bitcoin Native SegWit (Bech32, BIP-173) address generator.
 *
 * Reuses secp256k1 curve math from EthereumWalletGeneratorService
 * to keep implementation clean and dependency-free.
 */
class BitcoinWalletGeneratorService
{
    const P_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    const N_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const GX_HEX = '79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    const GY_HEX = '483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    const CHARSET = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';

    /**
     * Generate a new Bitcoin wallet (Native SegWit Bech32).
     *
     * @return array ['address' => 'bc1q...', 'private_key' => '0x...']
     */
    public function generateWallet(): array
    {
        $n = gmp_init(self::N_HEX, 16);
        do {
            $privBytes  = random_bytes(32);
            $privKeyHex = bin2hex($privBytes);
            $privKey    = gmp_init($privKeyHex, 16);
        } while (gmp_cmp($privKey, 1) < 0 || gmp_cmp($privKey, $n) >= 0);

        $address = $this->privateKeyToAddress($privKeyHex);

        return [
            'address'     => $address,
            'private_key' => '0x' . $privKeyHex,
        ];
    }

    /**
     * Derive Bech32 Native SegWit address from private key hex.
     */
    public function privateKeyToAddress(string $privateKeyHex): string
    {
        $privHex = ltrim($privateKeyHex, '0x');
        $pubHex  = $this->privateKeyToCompressedPublicKey($privHex);
        
        // RIPEMD-160(SHA-256(pubkey))
        $sha256 = hash('sha256', hex2bin($pubHex), true);
        $ripemd = hash('ripemd160', $sha256, true);
        
        return $this->encodeSegwitAddress('bc', 0, $ripemd);
    }

    /**
     * Compute compressed public key hex (33 bytes) from private key hex.
     */
    public function privateKeyToCompressedPublicKey(string $privateKeyHex): string
    {
        $p  = gmp_init(self::P_HEX, 16);
        $Gx = gmp_init(self::GX_HEX, 16);
        $Gy = gmp_init(self::GY_HEX, 16);
        $k  = gmp_init($privateKeyHex, 16);

        $eth = new EthereumWalletGeneratorService();
        [$x, $y] = $eth->pointMultiply($k, [$Gx, $Gy], $p);

        $prefix = (gmp_cmp(gmp_mod($y, gmp_init(2)), gmp_init(1)) === 0) ? '03' : '02';
        return $prefix . str_pad(gmp_strval($x, 16), 64, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a string is a valid Bitcoin address (supports legacy, SegWit, Bech32).
     */
    public static function isValidAddress(string $address): bool
    {
        $address = trim($address);
        
        // Native SegWit Mainnet Bech32 (starts with bc1q or bc1p)
        if (str_starts_with($address, 'bc1')) {
            $charsetPattern = '/^[a-z0-9]+$/';
            $cleaned = strtolower($address);
            if (!preg_match($charsetPattern, $cleaned)) {
                return false;
            }
            $len = strlen($cleaned);
            return $len >= 42 && $len <= 62; // Native SegWit is 42 chars (bc1q) or up to 62 chars (Taproot bc1p)
        }

        // Legacy 1... or SegWit nested 3... (Base58check)
        // Simple regex check for base58 format
        if (preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $address)) {
            return true;
        }

        return false;
    }

    /**
     * Encode a SegWit address (BIP-173).
     */
    public function encodeSegwitAddress(string $hrp, int $witnessVersion, string $witnessProgram): string
    {
        $programBytes = unpack('C*', $witnessProgram);
        $converted = self::convertBits(array_values($programBytes), 8, 5, true);
        $combined = array_merge([$witnessVersion], $converted);
        
        return self::bech32Encode($hrp, $combined);
    }

    /**
     * Bech32 polymod checksum.
     */
    private static function polymod(array $values): int
    {
        $generator = [0x3b6a57b2, 0x26508e6d, 0x1ea119fa, 0x3d4233dd, 0x2a1462b3];
        $chk = 1;
        foreach ($values as $value) {
            $top = $chk >> 25;
            $chk = (($chk & 0x1ffffff) << 5) ^ $value;
            for ($i = 0; $i < 5; $i++) {
                if (($top >> $i) & 1) {
                    $chk ^= $generator[$i];
                }
            }
        }
        return $chk;
    }

    /**
     * Expand human-readable part.
     */
    private static function hrpExpand(string $hrp): array
    {
        $ret = [];
        $len = strlen($hrp);
        for ($i = 0; $i < $len; $i++) {
            $ret[] = ord($hrp[$i]) >> 5;
        }
        $ret[] = 0;
        for ($i = 0; $i < $len; $i++) {
            $ret[] = ord($hrp[$i]) & 31;
        }
        return $ret;
    }

    /**
     * Create Bech32 checksum.
     */
    private static function createChecksum(string $hrp, array $data): array
    {
        $values = array_merge(self::hrpExpand($hrp), $data, [0, 0, 0, 0, 0, 0]);
        $polymod = self::polymod($values) ^ 1;
        $ret = [];
        for ($i = 0; $i < 6; $i++) {
            $ret[] = ($polymod >> (5 * (5 - $i))) & 31;
        }
        return $ret;
    }

    /**
     * Encode Bech32.
     */
    private static function bech32Encode(string $hrp, array $data): string
    {
        $checksum = self::createChecksum($hrp, $data);
        $combined = array_merge($data, $checksum);
        $address = $hrp . '1';
        foreach ($combined as $val) {
            $address .= self::CHARSET[$val];
        }
        return $address;
    }

    /**
     * Helper to convert bits (8-bit to 5-bit or vice versa).
     */
    public static function convertBits(array $data, int $fromBits, int $toBits, bool $pad = true): ?array
    {
        $acc = 0;
        $bits = 0;
        $ret = [];
        $maxv = (1 << $toBits) - 1;
        $max_acc = (1 << ($fromBits + $toBits - 1)) - 1;
        foreach ($data as $value) {
            if ($value < 0 || $value >> $fromBits) {
                return null;
            }
            $acc = (($acc << $fromBits) | $value) & $max_acc;
            $bits += $fromBits;
            while ($bits >= $toBits) {
                $bits -= $toBits;
                $ret[] = ($acc >> $bits) & $maxv;
            }
        }
        if ($pad) {
            if ($bits) {
                $ret[] = ($acc << ($toBits - $bits)) & $maxv;
            }
        } elseif ($bits >= $fromBits || (($acc << ($toBits - $bits)) & $maxv)) {
            return null;
        }
        return $ret;
    }

    private static function verifyChecksum(string $hrp, array $data): bool
    {
        return self::polymod(array_merge(self::hrpExpand($hrp), $data)) === 1;
    }

    /**
     * Decode a Bech32 address.
     */
    public static function bech32Decode(string $address): ?array
    {
        $address = strtolower(trim($address));
        $pos = strrpos($address, '1');
        if ($pos === false || $pos < 1 || $pos + 7 > strlen($address)) {
            return null;
        }
        $hrp = substr($address, 0, $pos);
        $dataStr = substr($address, $pos + 1);
        $data = [];
        for ($i = 0; $i < strlen($dataStr); $i++) {
            $char = $dataStr[$i];
            $idx = strpos(self::CHARSET, $char);
            if ($idx === false) {
                return null;
            }
            $data[] = $idx;
        }
        if (!self::verifyChecksum($hrp, $data)) {
            return null;
        }
        $data = array_slice($data, 0, -6);
        $witnessVersion = $data[0];
        $witnessProgram = self::convertBits(array_slice($data, 1), 5, 8, false);
        if ($witnessProgram === null) {
            return null;
        }
        return [
            'hrp' => $hrp,
            'witness_version' => $witnessVersion,
            'program' => pack('C*', ...$witnessProgram)
        ];
    }

    /**
     * Decode a Base58Check legacy or nested SegWit address.
     */
    public static function decodeBase58Address(string $address): ?array
    {
        try {
            $binary = SolanaBase58::decode($address);
            if (strlen($binary) !== 25) {
                return null;
            }
            $prefix = ord($binary[0]);
            $hash = substr($binary, 1, 20);
            $checksum = substr($binary, 21, 4);
            
            $calculatedChecksum = substr(hash('sha256', hash('sha256', substr($binary, 0, 21), true), true), 0, 4);
            if ($checksum !== $calculatedChecksum) {
                return null;
            }
            
            return [
                'prefix' => $prefix,
                'hash' => $hash
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Translate any valid Bitcoin address format into its matching scriptPubKey.
     */
    public static function addressToScriptPubKey(string $address): ?string
    {
        $address = trim($address);
        
        // Native SegWit Bech32
        if (str_starts_with(strtolower($address), 'bc1') || str_starts_with(strtolower($address), 'tb1')) {
            $decoded = self::bech32Decode($address);
            if ($decoded) {
                $version = $decoded['witness_version'];
                $program = $decoded['program'];
                $len = strlen($program);
                return sprintf('%02x%02x%s', $version, $len, bin2hex($program));
            }
        }
        
        // Base58 Check (Legacy P2PKH or Nested SegWit P2SH)
        $decoded = self::decodeBase58Address($address);
        if ($decoded) {
            $prefix = $decoded['prefix'];
            $hashHex = bin2hex($decoded['hash']);
            
            if ($prefix === 0x00 || $prefix === 0x6f) { // 0x6f is testnet legacy
                return '76a914' . $hashHex . '88ac';
            } elseif ($prefix === 0x05 || $prefix === 0xc4) { // 0xc4 is testnet P2SH
                return 'a914' . $hashHex . '87';
            }
        }
        
        return null;
    }
}
