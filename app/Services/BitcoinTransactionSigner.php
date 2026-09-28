<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Builds, signs, and broadcasts Native SegWit (P2WPKH) Bitcoin transactions.
 * 
 * Implements BIP-143 transaction hashing, offline ECDSA DER signing,
 * and Esplora API integration.
 */
class BitcoinTransactionSigner
{
    const N_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const P_HEX  = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';
    const GX_HEX = '79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798';
    const GY_HEX = '483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

    /**
     * Query UTXOs for a given Bitcoin address from Esplora.
     */
    public static function fetchUtxos(string $rpcUrl, string $address): array
    {
        try {
            $url = rtrim($rpcUrl, '/') . '/address/' . $address . '/utxo';
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            if ($response->successful()) {
                return $response->json() ?: [];
            }
        } catch (\Exception $e) {
            Log::error("Bitcoin fetchUtxos failed for {$address}: " . $e->getMessage());
        }
        return [];
    }

    /**
     * Broadcast transaction raw hex.
     */
    public static function broadcastTransaction(string $rpcUrl, string $rawTxHex): ?string
    {
        try {
            $url = rtrim($rpcUrl, '/') . '/tx';
            $response = Http::withoutVerifying()->timeout(15)
                ->withBody($rawTxHex, 'text/plain')
                ->post($url);
                
            if ($response->successful()) {
                return trim($response->body());
            }
            Log::error("Bitcoin broadcast failed: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Bitcoin broadcast exception: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Build an unsigned Native SegWit transaction.
     */
    public static function buildUnsignedSegwitTx(
        array $utxos,
        string $toAddress,
        int $amountSat,
        string $changeAddress,
        int $feeRateSatVb = 20
    ): ?array {
        $scriptPubKeyDest = BitcoinWalletGeneratorService::addressToScriptPubKey($toAddress);
        $scriptPubKeyChange = BitcoinWalletGeneratorService::addressToScriptPubKey($changeAddress);

        if (!$scriptPubKeyDest) {
            Log::error("Bitcoin buildUnsignedSegwitTx: invalid destination address {$toAddress}");
            return null;
        }

        // 1. UTXO Selection & Input assembly
        $inputs = [];
        $totalInputSat = 0;
        foreach ($utxos as $utxo) {
            $address = $changeAddress; // assuming inputs are from the sender
            $scriptPubKeyIn = BitcoinWalletGeneratorService::addressToScriptPubKey($address);
            
            $inputs[] = [
                'txid' => $utxo['txid'],
                'vout' => (int) $utxo['vout'],
                'sequence' => 0xffffffff,
                'amount' => (int) $utxo['value'],
                'scriptPubKey' => $scriptPubKeyIn,
                'witness' => []
            ];
            $totalInputSat += (int) $utxo['value'];
            
            // If we have selected enough UTXOs to cover the target amount + a loose buffer
            if ($totalInputSat >= $amountSat + 50000) {
                break;
            }
        }

        if ($totalInputSat < $amountSat) {
            Log::error("Bitcoin buildUnsignedSegwitTx: Insufficient funds. Total inputs: {$totalInputSat} Sat, target: {$amountSat} Sat");
            return null;
        }

        // 2. Estimate Fee
        // Overhead = 11 vBytes. Input = 68 vBytes. Output = 31 vBytes.
        $numInputs = count($inputs);
        // We will have 1 dest output, and potentially 1 change output
        $numOutputs = 2; 
        $estimatedVSize = ($numInputs * 68) + ($numOutputs * 31) + 11;
        $feeSat = $estimatedVSize * $feeRateSatVb;

        $changeSat = $totalInputSat - $amountSat - $feeSat;

        // Re-estimate if change is 0/dust
        $outputs = [];
        $outputs[] = [
            'amount' => $amountSat,
            'scriptPubKey' => $scriptPubKeyDest
        ];

        if ($changeSat >= 546) { // Dust limit is 546 Satoshis
            $outputs[] = [
                'amount' => $changeSat,
                'scriptPubKey' => $scriptPubKeyChange
            ];
        } else {
            // No change output, recalculate fee with 1 output
            $numOutputs = 1;
            $estimatedVSize = ($numInputs * 68) + ($numOutputs * 31) + 11;
            $feeSat = $estimatedVSize * $feeRateSatVb;
            
            // Adjust destination amount to consume the excess (or keep fee high)
            $amountSat = $totalInputSat - $feeSat;
            if ($amountSat <= 0) {
                return null;
            }
            $outputs[0]['amount'] = $amountSat;
        }

        return [
            'version' => 1,
            'inputs' => $inputs,
            'outputs' => $outputs,
            'locktime' => 0
        ];
    }

    /**
     * Sign the raw transaction and return the serialized signed hex.
     */
    public static function signTransaction(array $tx, string $privateKeyHex): ?string
    {
        $walletService = new BitcoinWalletGeneratorService();
        $compressedPubKey = $walletService->privateKeyToCompressedPublicKey($privateKeyHex);

        $n  = gmp_init(self::N_HEX, 16);
        $p  = gmp_init(self::P_HEX, 16);
        $Gx = gmp_init(self::GX_HEX, 16);
        $Gy = gmp_init(self::GY_HEX, 16);
        $d  = gmp_init(ltrim($privateKeyHex, '0x'), 16);

        $curve = new EthereumWalletGeneratorService();

        // 1. Sign each input
        foreach ($tx['inputs'] as $i => &$input) {
            $sighash = self::getWitnessSighash($tx, $i);
            if (!$sighash) {
                return null;
            }

            $z = gmp_init(bin2hex($sighash), 16);
            $k = self::rfc6979(bin2hex($sighash), ltrim($privateKeyHex, '0x'), $n);

            // R = k · G
            [$Rx, $Ry] = $curve->pointMultiply($k, [$Gx, $Gy], $p);

            // r = Rx mod n
            $r = gmp_mod($Rx, $n);

            // s = k⁻¹ · (z + r·d) mod n
            $s = gmp_mod(gmp_mul(gmp_invert($k, $n), gmp_add($z, gmp_mul($r, $d))), $n);

            // BIP-62 low-S normalisation
            if (gmp_cmp($s, gmp_div_q($n, 2)) > 0) {
                $s = gmp_sub($n, $s);
            }

            $der = self::encodeDer($r, $s);
            
            // Append SIGHASH_ALL flag (0x01)
            $sigHex = bin2hex($der . "\x01");

            $input['witness'] = [
                $sigHex,
                $compressedPubKey
            ];
        }

        return self::serializeSignedTx($tx);
    }

    /**
     * Compute BIP-143 witness signature hash for input index.
     */
    private static function getWitnessSighash(array $tx, int $inputIndex): ?string
    {
        $input = $tx['inputs'][$inputIndex];
        
        // P2WPKH scriptPubKey starts with 0014 followed by the 20-byte pubkey hash
        $scriptPubKey = $input['scriptPubKey'];
        if (strlen($scriptPubKey) !== 44 || substr($scriptPubKey, 0, 4) !== '0014') {
            Log::error("Bitcoin TransactionSigner: scriptPubKey must be a P2WPKH address program.");
            return null;
        }
        $pubkeyHash = substr($scriptPubKey, 4);

        // 1. Version
        $versionBin = pack('V', $tx['version']);

        // 2. hashPrevouts (Double SHA-256 of all input outpoints concatenated)
        $prevouts = '';
        foreach ($tx['inputs'] as $in) {
            $prevouts .= strrev(hex2bin($in['txid'])) . pack('V', $in['vout']);
        }
        $hashPrevouts = hash('sha256', hash('sha256', $prevouts, true), true);

        // 3. hashSequence (Double SHA-256 of all sequences concatenated)
        $sequences = '';
        foreach ($tx['inputs'] as $in) {
            $sequences .= pack('V', $in['sequence']);
        }
        $hashSequence = hash('sha256', hash('sha256', $sequences, true), true);

        // 4. outpoint
        $outpoint = strrev(hex2bin($input['txid'])) . pack('V', $input['vout']);

        // 5. scriptCode: OP_DUP OP_HASH160 [20-byte pubkey hash] OP_EQUALVERIFY OP_CHECKSIG
        // Length of this scriptCode is 25 bytes (0x19)
        $scriptCode = hex2bin('1976a914' . $pubkeyHash . '88ac');

        // 6. value (8 bytes little-endian)
        $valueBin = pack('P', $input['amount']);

        // 7. nSequence
        $sequenceBin = pack('V', $input['sequence']);

        // 8. hashOutputs (Double SHA-256 of all outputs serialized)
        $outputs = '';
        foreach ($tx['outputs'] as $out) {
            $outputs .= pack('P', $out['amount']) 
                     . self::writeVarInt(strlen(hex2bin($out['scriptPubKey']))) 
                     . hex2bin($out['scriptPubKey']);
        }
        $hashOutputs = hash('sha256', hash('sha256', $outputs, true), true);

        // 9. nLockTime
        $lockTimeBin = pack('V', $tx['locktime']);

        // 10. nHashType (0x00000001 SIGHASH_ALL)
        $hashTypeBin = pack('V', 1);

        $preimage = $versionBin
                  . $hashPrevouts
                  . $hashSequence
                  . $outpoint
                  . $scriptCode
                  . $valueBin
                  . $sequenceBin
                  . $hashOutputs
                  . $lockTimeBin
                  . $hashTypeBin;

        return hash('sha256', hash('sha256', $preimage, true), true);
    }

    /**
     * DER Encode r and s values of ECDSA signature.
     */
    private static function encodeDer(\GMP $r, \GMP $s): string
    {
        $rHex = gmp_strval($r, 16);
        $sHex = gmp_strval($s, 16);

        if (strlen($rHex) % 2 !== 0) $rHex = '0' . $rHex;
        if (strlen($sHex) % 2 !== 0) $sHex = '0' . $sHex;

        if (hexdec(substr($rHex, 0, 2)) >= 128) {
            $rHex = '00' . $rHex;
        }
        if (hexdec(substr($sHex, 0, 2)) >= 128) {
            $sHex = '00' . $sHex;
        }

        $rBin = hex2bin($rHex);
        $sBin = hex2bin($sHex);

        $rPart = "\x02" . chr(strlen($rBin)) . $rBin;
        $sPart = "\x02" . chr(strlen($sBin)) . $sBin;

        $derPayload = $rPart . $sPart;
        return "\x30" . chr(strlen($derPayload)) . $derPayload;
    }

    /**
     * Serialize standard signed SegWit transaction structure into hex.
     */
    private static function serializeSignedTx(array $tx): string
    {
        $hex = '';

        // 1. Version
        $hex .= bin2hex(pack('V', $tx['version']));

        // 2. Marker and Flag for SegWit
        $hex .= '0001';

        // 3. Inputs Count
        $hex .= bin2hex(self::writeVarInt(count($tx['inputs'])));

        // 4. Inputs List
        foreach ($tx['inputs'] as $input) {
            $hex .= bin2hex(strrev(hex2bin($input['txid'])));
            $hex .= bin2hex(pack('V', $input['vout']));
            $hex .= '00'; // ScriptSig length is 00 for SegWit in Tx serialization
            $hex .= bin2hex(pack('V', $input['sequence']));
        }

        // 5. Outputs Count
        $hex .= bin2hex(self::writeVarInt(count($tx['outputs'])));

        // 6. Outputs List
        foreach ($tx['outputs'] as $output) {
            $hex .= bin2hex(pack('P', $output['amount']));
            $hex .= bin2hex(self::writeVarInt(strlen(hex2bin($output['scriptPubKey']))));
            $hex .= $output['scriptPubKey'];
        }

        // 7. Witness Data List (ordered by input index)
        foreach ($tx['inputs'] as $input) {
            $hex .= bin2hex(self::writeVarInt(count($input['witness'])));
            foreach ($input['witness'] as $wItem) {
                $hex .= bin2hex(self::writeVarInt(strlen(hex2bin($wItem))));
                $hex .= $wItem;
            }
        }

        // 8. Locktime
        $hex .= bin2hex(pack('V', $tx['locktime']));

        return $hex;
    }

    /**
     * Write variable length integer.
     */
    private static function writeVarInt(int $i): string
    {
        if ($i < 0xfd) {
            return chr($i);
        } elseif ($i <= 0xffff) {
            return "\xfd" . pack('v', $i);
        } elseif ($i <= 0xffffffff) {
            return "\xfe" . pack('V', $i);
        } else {
            return "\xff" . pack('P', $i);
        }
    }

    /**
     * RFC 6979 deterministic k generator.
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
