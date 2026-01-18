<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Support;

use InvalidArgumentException;

class Encoder
{
    /**
     * Base62 character set (URL-safe, compact, case-sensitive).
     */
    public const BASE62_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * Base36 character set (case-insensitive, slightly longer).
     */
    public const BASE36_CHARS = '0123456789abcdefghijklmnopqrstuvwxyz';

    protected string $charset;

    protected int $base;

    public function __construct(?string $encodingType = null)
    {
        $encodingType ??= config('muid.encoding.type', 'base62');

        $this->charset = match ($encodingType) {
            'base36' => self::BASE36_CHARS,
            'base62' => self::BASE62_CHARS,
            default => throw new InvalidArgumentException("Unsupported encoding type: {$encodingType}"),
        };

        $this->base = strlen($this->charset);
    }

    /**
     * Get the current encoding type.
     */
    public function getEncodingType(): string
    {
        return $this->base === 62 ? 'base62' : 'base36';
    }

    /**
     * Get the character set being used.
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Get the base (62 or 36).
     */
    public function getBase(): int
    {
        return $this->base;
    }

    /**
     * Encode an integer to a base62/36 string.
     */
    public function encodeInt(int $number): string
    {
        if ($number < 0) {
            throw new InvalidArgumentException('Number must be non-negative');
        }

        if ($number === 0) {
            return $this->charset[0];
        }

        $result = '';

        while ($number > 0) {
            $result = $this->charset[$number % $this->base].$result;
            $number = intdiv($number, $this->base);
        }

        return $result;
    }

    /**
     * Decode a base62/36 string to an integer.
     */
    public function decodeInt(string $encoded): int
    {
        $number = 0;
        $length = strlen($encoded);

        for ($i = 0; $i < $length; $i++) {
            $char = $encoded[$i];
            $value = strpos($this->charset, $char);

            if ($value === false) {
                throw new InvalidArgumentException("Invalid character in encoded string: {$char}");
            }

            $number = $number * $this->base + $value;
        }

        return $number;
    }

    /**
     * Encode an integer to a fixed-length base62/36 string (left-padded).
     */
    public function encodeIntPadded(int $number, int $length): string
    {
        $encoded = $this->encodeInt($number);

        if (strlen($encoded) > $length) {
            throw new InvalidArgumentException("Encoded value exceeds maximum length of {$length}");
        }

        return str_pad($encoded, $length, $this->charset[0], STR_PAD_LEFT);
    }

    /**
     * Encode binary data (bytes) to base62/36 string.
     */
    public function encodeBytes(string $bytes): string
    {
        if ($bytes === '') {
            return '';
        }

        // Convert bytes to a large integer using GMP or BCMath
        $hex = bin2hex($bytes);

        if (function_exists('gmp_init')) {
            $number = gmp_init($hex, 16);

            return $this->encodeGmp($number);
        }

        // Fallback to BCMath
        $number = $this->hexToBcmath($hex);

        return $this->encodeBcmath($number);
    }

    /**
     * Decode base62/36 string to binary data (bytes).
     */
    public function decodeBytes(string $encoded): string
    {
        if ($encoded === '') {
            return '';
        }

        if (function_exists('gmp_init')) {
            $number = $this->decodeGmp($encoded);
            $hex = gmp_strval($number, 16);
        } else {
            $number = $this->decodeBcmath($encoded);
            $hex = $this->bcmathToHex($number);
        }

        // Ensure even length for hex2bin
        if (strlen($hex) % 2 !== 0) {
            $hex = '0'.$hex;
        }

        return hex2bin($hex);
    }

    /**
     * Encode bytes to a fixed-length base62/36 string.
     */
    public function encodeBytesPadded(string $bytes, int $length): string
    {
        $encoded = $this->encodeBytes($bytes);

        if (strlen($encoded) > $length) {
            throw new InvalidArgumentException("Encoded value exceeds maximum length of {$length}");
        }

        return str_pad($encoded, $length, $this->charset[0], STR_PAD_LEFT);
    }

    /**
     * Generate random base62/36 string of specified length.
     */
    public function randomString(int $length): string
    {
        $result = '';
        $charsetLength = $this->base;

        for ($i = 0; $i < $length; $i++) {
            $result .= $this->charset[random_int(0, $charsetLength - 1)];
        }

        return $result;
    }

    /**
     * Validate that a string only contains valid characters for the encoding.
     */
    public function isValid(string $encoded): bool
    {
        if ($encoded === '') {
            return true;
        }

        $pattern = '/^['.preg_quote($this->charset, '/').']+$/';

        return preg_match($pattern, $encoded) === 1;
    }

    /**
     * Encode timestamp (milliseconds) to base62/36 string.
     */
    public function encodeTimestamp(int $timestampMs, int $length = 8): string
    {
        return $this->encodeIntPadded($timestampMs, $length);
    }

    /**
     * Decode base62/36 string to timestamp (milliseconds).
     */
    public function decodeTimestamp(string $encoded): int
    {
        return $this->decodeInt($encoded);
    }

    /**
     * Encode using GMP extension.
     */
    protected function encodeGmp(\GMP $number): string
    {
        if (gmp_cmp($number, 0) === 0) {
            return $this->charset[0];
        }

        $result = '';
        $base = gmp_init($this->base);

        while (gmp_cmp($number, 0) > 0) {
            $remainder = gmp_intval(gmp_mod($number, $base));
            $result = $this->charset[$remainder].$result;
            $number = gmp_div_q($number, $base);
        }

        return $result;
    }

    /**
     * Decode using GMP extension.
     */
    protected function decodeGmp(string $encoded): \GMP
    {
        $number = gmp_init(0);
        $base = gmp_init($this->base);
        $length = strlen($encoded);

        for ($i = 0; $i < $length; $i++) {
            $char = $encoded[$i];
            $value = strpos($this->charset, $char);

            if ($value === false) {
                throw new InvalidArgumentException("Invalid character in encoded string: {$char}");
            }

            $number = gmp_add(gmp_mul($number, $base), $value);
        }

        return $number;
    }

    /**
     * Encode using BCMath extension.
     */
    protected function encodeBcmath(string $number): string
    {
        if (bccomp($number, '0') === 0) {
            return $this->charset[0];
        }

        $result = '';
        $base = (string) $this->base;

        while (bccomp($number, '0') > 0) {
            $remainder = (int) bcmod($number, $base);
            $result = $this->charset[$remainder].$result;
            $number = bcdiv($number, $base, 0);
        }

        return $result;
    }

    /**
     * Decode using BCMath extension.
     */
    protected function decodeBcmath(string $encoded): string
    {
        $number = '0';
        $base = (string) $this->base;
        $length = strlen($encoded);

        for ($i = 0; $i < $length; $i++) {
            $char = $encoded[$i];
            $value = strpos($this->charset, $char);

            if ($value === false) {
                throw new InvalidArgumentException("Invalid character in encoded string: {$char}");
            }

            $number = bcadd(bcmul($number, $base), (string) $value);
        }

        return $number;
    }

    /**
     * Convert hex string to BCMath number.
     */
    protected function hexToBcmath(string $hex): string
    {
        $number = '0';
        $length = strlen($hex);

        for ($i = 0; $i < $length; $i++) {
            $digit = hexdec($hex[$i]);
            $number = bcadd(bcmul($number, '16'), (string) $digit);
        }

        return $number;
    }

    /**
     * Convert BCMath number to hex string.
     */
    protected function bcmathToHex(string $number): string
    {
        $hex = '';

        while (bccomp($number, '0') > 0) {
            $remainder = (int) bcmod($number, '16');
            $hex = dechex($remainder).$hex;
            $number = bcdiv($number, '16', 0);
        }

        return $hex ?: '0';
    }
}
