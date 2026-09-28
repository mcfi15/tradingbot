<?php

namespace App\Services;

class SolanaBase58
{
    private static $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Decode a Base58 string into binary string.
     *
     * @param string $base58
     * @return string
     */
    public static function decode(string $base58): string
    {
        $bytes = [0];
        $length = strlen($base58);

        for ($i = 0; $i < $length; $i++) {
            $char = $base58[$i];
            $value = strpos(self::$alphabet, $char);
            if ($value === false) {
                throw new \InvalidArgumentException("Invalid Base58 character: $char");
            }

            $carry = $value;
            $bytesCount = count($bytes);
            for ($j = 0; $j < $bytesCount; $j++) {
                $carry += $bytes[$j] * 58;
                $bytes[$j] = $carry & 0xff;
                $carry >>= 8;
            }

            while ($carry > 0) {
                $bytes[] = $carry & 0xff;
                $carry >>= 8;
            }
        }

        // Add leading zeroes matching leading '1's
        for ($i = 0; $i < $length && $base58[$i] === '1'; $i++) {
            $bytes[] = 0;
        }

        return pack('C*', ...array_reverse($bytes));
    }

    /**
     * Encode binary string into Base58 string.
     *
     * @param string $bytes
     * @return string
     */
    public static function encode(string $bytes): string
    {
        $length = strlen($bytes);
        if ($length === 0) {
            return '';
        }

        $chars = array_fill(0, $length * 2, 0);
        $charCount = 0;

        for ($i = 0; $i < $length; $i++) {
            $carry = ord($bytes[$i]);
            for ($j = 0; $j < $charCount; $j++) {
                $carry += $chars[$j] << 8;
                $chars[$j] = $carry % 58;
                $carry = intdiv($carry, 58);
            }
            while ($carry > 0) {
                $chars[$charCount++] = $carry % 58;
                $carry = intdiv($carry, 58);
            }
        }

        $result = '';
        for ($i = 0; $i < $length && ord($bytes[$i]) === 0; $i++) {
            $result .= '1';
        }

        for ($i = $charCount - 1; $i >= 0; $i--) {
            $result .= self::$alphabet[$chars[$i]];
        }

        return $result;
    }
}
