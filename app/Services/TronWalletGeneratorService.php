<?php

namespace App\Services;

class TronWalletGeneratorService
{
    public function generateWallet(): array
    {
        $ethService = new EthereumWalletGeneratorService();
        $wallet = $ethService->generateWallet();

        return [
            'address' => $this->ethereumAddressToTronAddress($wallet['address']),
            'private_key' => $wallet['private_key'],
        ];
    }

    public function ethereumAddressToTronAddress(string $ethAddress): string
    {
        $hex = strtolower(ltrim($ethAddress, '0x'));
        if (strlen($hex) !== 40) {
            throw new \InvalidArgumentException('Invalid Ethereum address supplied for Tron conversion.');
        }

        $tronHex = '41' . $hex;
        return $this->base58CheckEncode(hex2bin($tronHex));
    }

    public function isValidAddress(string $address): bool
    {
        return preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $address) === 1;
    }

    public function tronAddressToHex(string $address): string
    {
        if (!$this->isValidAddress($address)) {
            throw new \InvalidArgumentException('Invalid Tron address supplied for hex conversion.');
        }

        $payload = $this->base58CheckDecode($address);

        return strtoupper(bin2hex($payload));
    }

    private function base58CheckEncode(string $binary): string
    {
        $checksum = substr(hash('sha256', hash('sha256', $binary, true), true), 0, 4);
        return $this->base58Encode($binary . $checksum);
    }

    private function base58CheckDecode(string $input): string
    {
        $decoded = $this->base58Decode($input);

        if (strlen($decoded) < 5) {
            throw new \InvalidArgumentException('Invalid Tron address payload.');
        }

        $payload = substr($decoded, 0, -4);
        $checksum = substr($decoded, -4);
        $expected = substr(hash('sha256', hash('sha256', $payload, true), true), 0, 4);

        if (!hash_equals($expected, $checksum)) {
            throw new \InvalidArgumentException('Invalid Tron address checksum.');
        }

        return $payload;
    }

    private function base58Encode(string $binary): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $bytes = array_values(unpack('C*', $binary));

        $digits = [0];
        foreach ($bytes as $byte) {
            $carry = $byte;
            for ($i = 0, $count = count($digits); $i < $count; $i++) {
                $carry += $digits[$i] << 8;
                $digits[$i] = $carry % 58;
                $carry = intdiv($carry, 58);
            }
            while ($carry > 0) {
                $digits[] = $carry % 58;
                $carry = intdiv($carry, 58);
            }
        }

        $result = '';
        foreach ($bytes as $byte) {
            if ($byte === 0) {
                $result .= '1';
            } else {
                break;
            }
        }

        for ($i = count($digits) - 1; $i >= 0; $i--) {
            $result .= $alphabet[$digits[$i]];
        }

        return $result;
    }

    private function base58Decode(string $input): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $indexes = array_flip(str_split($alphabet));

        $bytes = [0];
        foreach (str_split($input) as $char) {
            if (!isset($indexes[$char])) {
                throw new \InvalidArgumentException('Invalid Base58 character in Tron address.');
            }

            $carry = $indexes[$char];
            for ($i = 0, $count = count($bytes); $i < $count; $i++) {
                $carry += $bytes[$i] * 58;
                $bytes[$i] = $carry & 0xff;
                $carry >>= 8;
            }

            while ($carry > 0) {
                $bytes[] = $carry & 0xff;
                $carry >>= 8;
            }
        }

        $leadingZeroes = 0;
        for ($i = 0; $i < strlen($input) && $input[$i] === '1'; $i++) {
            $leadingZeroes++;
        }

        $decoded = str_repeat("\x00", $leadingZeroes);
        for ($i = count($bytes) - 1; $i >= 0; $i--) {
            $decoded .= chr($bytes[$i]);
        }

        return $decoded;
    }
}