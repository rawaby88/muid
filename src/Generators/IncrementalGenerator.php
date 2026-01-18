<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Generators;

use Illuminate\Support\Facades\DB;
use Rawaby88\Muid\Contracts\MuidGenerator;
use Rawaby88\Muid\Support\MuidComponents;

class IncrementalGenerator implements MuidGenerator
{
    /**
     * In-memory cache for sequences (per-request).
     *
     * @var array<string, int>
     */
    protected static array $sequenceCache = [];

    public function generate(string $prefix, ?int $maxLength = null): string
    {
        $maxLength ??= config('muid.lengths.standard', 36);

        // Get the next sequence number
        $sequence = $this->getNextSequence($prefix);

        // Calculate max digits available for body
        $prefixLength = strlen($prefix) + 1; // +1 for separator
        $maxDigits = $maxLength - $prefixLength;

        // Check if sequence fits
        $sequenceStr = (string) $sequence;
        if (strlen($sequenceStr) > $maxDigits) {
            throw new \OverflowException(
                "Sequence {$sequence} exceeds maximum length of {$maxDigits} digits for prefix '{$prefix}'"
            );
        }

        return $prefix.'_'.$sequenceStr;
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

        // Validate body is numeric
        if (! ctype_digit($body)) {
            return null;
        }

        $sequence = (int) $body;

        return new MuidComponents(
            prefix: $prefix,
            body: $body,
            strategy: $this->getStrategy(),
            sequence: $sequence,
        );
    }

    public function getStrategy(): string
    {
        return 'incremental';
    }

    /**
     * Get the next sequence number for a prefix.
     */
    protected function getNextSequence(string $prefix): int
    {
        $perPrefix = config('muid.incremental.per_prefix', true);

        $cacheKey = $perPrefix ? $prefix : '__global__';

        // Check if we have a cached sequence
        if (isset(static::$sequenceCache[$cacheKey])) {
            return ++static::$sequenceCache[$cacheKey];
        }

        // Query database for max existing sequence
        $maxSequence = $this->queryMaxSequence($prefix, $perPrefix);

        static::$sequenceCache[$cacheKey] = $maxSequence + 1;

        return static::$sequenceCache[$cacheKey];
    }

    /**
     * Query the database for the maximum existing sequence.
     */
    protected function queryMaxSequence(string $prefix, bool $perPrefix): int
    {
        // This is a simplified implementation.
        // In a real application, you'd query all tables with MUIDs
        // or maintain a separate sequence table.
        return 0;
    }

    /**
     * Reset the sequence cache (useful for testing).
     */
    public static function resetSequenceCache(): void
    {
        static::$sequenceCache = [];
    }

    /**
     * Set a specific sequence value (useful for testing or resuming sequences).
     */
    public static function setSequence(string $prefix, int $value): void
    {
        $perPrefix = config('muid.incremental.per_prefix', true);
        $cacheKey = $perPrefix ? $prefix : '__global__';
        static::$sequenceCache[$cacheKey] = $value;
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
