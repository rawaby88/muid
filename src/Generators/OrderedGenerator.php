<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Generators;

use Rawaby88\Muid\Contracts\MuidGenerator;
use Rawaby88\Muid\Support\Encoder;
use Rawaby88\Muid\Support\MuidComponents;

class OrderedGenerator implements MuidGenerator
{
    protected Encoder $encoder;

    protected int $timestampLength = 8;

    protected int $signatureLength;

    protected bool $signatureEnabled;

    public function __construct(?Encoder $encoder = null)
    {
        $this->encoder = $encoder ?? new Encoder;
        $this->signatureEnabled = config('muid.signature.enabled', false);
        $this->signatureLength = $this->signatureEnabled ? config('muid.signature.length', 4) : 0;
    }

    public function generate(string $prefix, ?int $maxLength = null): string
    {
        $maxLength ??= config('muid.lengths.standard', 36);

        // Calculate available length for body (excluding prefix and separator)
        $prefixLength = strlen($prefix) + 1; // +1 for separator
        $bodyLength = $maxLength - $prefixLength;

        // Timestamp (8 chars) + random + signature
        $randomLength = $bodyLength - $this->timestampLength - $this->signatureLength;

        if ($randomLength < 4) {
            throw new \InvalidArgumentException(
                "Max length {$maxLength} is too short for prefix '{$prefix}' with ordered strategy"
            );
        }

        // Generate timestamp component (milliseconds since epoch)
        $timestamp = (int) (microtime(true) * 1000);
        $timestampEncoded = $this->encoder->encodeTimestamp($timestamp, $this->timestampLength);

        // Generate random component
        $random = $this->encoder->randomString($randomLength);

        // Build body
        $body = $timestampEncoded.$random;

        // Add signature if enabled
        if ($this->signatureEnabled) {
            $signature = $this->generateSignature($prefix, $body);
            $body .= $signature;
        }

        return $prefix.'_'.$body;
    }

    public function validate(string $muid, ?string $expectedPrefix = null): bool
    {
        $components = $this->parse($muid);

        if ($components === null) {
            return false;
        }

        if ($expectedPrefix !== null && $components->prefix !== $expectedPrefix) {
            return false;
        }

        // Validate signature if enabled
        if ($this->signatureEnabled && $components->signature !== null) {
            $bodyWithoutSignature = substr($components->body, 0, -$this->signatureLength);
            $expectedSignature = $this->generateSignature($components->prefix, $bodyWithoutSignature);

            if ($components->signature !== $expectedSignature) {
                return false;
            }
        }

        return true;
    }

    public function parse(string $muid): ?MuidComponents
    {
        // Split by separator
        $parts = explode('_', $muid, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$prefix, $body] = $parts;

        // Validate prefix
        if (! $this->isValidPrefix($prefix)) {
            return null;
        }

        // Validate body contains only valid characters
        if (! $this->encoder->isValid($body)) {
            return null;
        }

        // Check minimum body length
        $minBodyLength = $this->timestampLength + 4; // timestamp + at least 4 random chars
        if (strlen($body) < $minBodyLength) {
            return null;
        }

        // Extract components
        $timestamp = substr($body, 0, $this->timestampLength);
        $timestampMs = $this->encoder->decodeTimestamp($timestamp);

        $signature = null;
        $random = null;

        if ($this->signatureEnabled && strlen($body) > $this->timestampLength + $this->signatureLength) {
            $signature = substr($body, -$this->signatureLength);
            $random = substr($body, $this->timestampLength, -$this->signatureLength);
        } else {
            $random = substr($body, $this->timestampLength);
        }

        return new MuidComponents(
            prefix: $prefix,
            body: $body,
            strategy: $this->getStrategy(),
            timestamp: $timestampMs,
            random: $random,
            signature: $signature,
        );
    }

    public function getStrategy(): string
    {
        return 'ordered';
    }

    /**
     * Generate a signature for the given prefix and body.
     */
    protected function generateSignature(string $prefix, string $body): string
    {
        $appKey = config('app.key', 'default-key');
        $data = $prefix.$body.$appKey;

        // Use xxHash if available, otherwise fall back to crc32
        if (function_exists('xxhash64')) {
            $hash = xxhash64($data);
        } else {
            $hash = hash('crc32b', $data);
        }

        // Convert hash to base62 and take required length
        $encoded = $this->encoder->encodeBytes(hex2bin($hash));

        return substr($encoded, 0, $this->signatureLength);
    }

    /**
     * Validate a prefix according to configuration rules.
     */
    protected function isValidPrefix(string $prefix): bool
    {
        $minLength = config('muid.prefix.min_length', 2);
        $maxLength = config('muid.prefix.max_length', 8);
        $pattern = config('muid.prefix.pattern', '/^[a-z][a-z0-9]*$/i');

        $length = strlen($prefix);

        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        return preg_match($pattern, $prefix) === 1;
    }
}
