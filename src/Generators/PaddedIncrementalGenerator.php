<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Generators;

use Rawaby88\Muid\Contracts\MuidGenerator;
use Rawaby88\Muid\Support\MuidComponents;

class PaddedIncrementalGenerator implements MuidGenerator
{
    /**
     * In-memory cache for sequences (per-request).
     *
     * @var array<string, int>
     */
    protected static array $sequenceCache = [];

    protected int $paddingLength;

    protected string $paddingCharacter;

    public function __construct()
    {
        $this->paddingLength = config('muid.incremental.padding_length', 7);
        $this->paddingCharacter = config('muid.incremental.padding_character', '0');
    }

    public function generate(string $prefix, ?int $maxLength = null): string
    {
        $maxLength ??= config('muid.lengths.standard', 36);

        // Get the next sequence number
        $sequence = $this->getNextSequence($prefix);

        // Calculate available space for body
        $prefixLength = strlen($prefix) + 1; // +1 for separator
        $availableLength = $maxLength - $prefixLength;

        // Use the smaller of configured padding or available space
        $actualPadding = min($this->paddingLength, $availableLength);

        // Check if sequence fits in padding
        $maxSequence = (int) str_repeat('9', $actualPadding);
        if ($sequence > $maxSequence) {
            throw new \OverflowException(
                "Sequence {$sequence} exceeds maximum value of {$maxSequence} for padding length {$actualPadding}"
            );
        }

        $paddedSequence = str_pad((string) $sequence, $actualPadding, $this->paddingCharacter, STR_PAD_LEFT);

        return $prefix.'_'.$paddedSequence;
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

        // Validate body is numeric (including leading zeros)
        if (! ctype_digit($body)) {
            return null;
        }

        // Remove leading zeros to get actual sequence
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
        return 'padded';
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
     * Get the configured padding length.
     */
    public function getPaddingLength(): int
    {
        return $this->paddingLength;
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
