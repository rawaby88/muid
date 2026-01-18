<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Support;

class MuidValidator
{
    protected MuidFactory $factory;

    public function __construct(MuidFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Validate a MUID.
     *
     * @param  string  $muid  The MUID to validate
     * @param  string|null  $expectedPrefix  Expected prefix (optional)
     * @param  string|null  $strategy  Strategy to use for validation (optional, auto-detects if null)
     */
    public function validate(string $muid, ?string $expectedPrefix = null, ?string $strategy = null): bool
    {
        // Basic format validation
        if (! $this->hasValidFormat($muid)) {
            return false;
        }

        $parts = explode('_', $muid, 2);
        [$prefix, $body] = $parts;

        // Check expected prefix
        if ($expectedPrefix !== null && $prefix !== $expectedPrefix) {
            return false;
        }

        // Validate prefix format
        if (! $this->isValidPrefix($prefix)) {
            return false;
        }

        // If no strategy specified, try to detect and validate
        if ($strategy === null) {
            $strategy = $this->detectStrategy($body);
        }

        // Validate using the appropriate generator
        $generator = $this->factory->getGenerator($strategy);

        return $generator->validate($muid, $expectedPrefix);
    }

    /**
     * Check if the MUID has a valid basic format.
     */
    public function hasValidFormat(string $muid): bool
    {
        // Must contain exactly one underscore
        if (substr_count($muid, '_') !== 1) {
            return false;
        }

        $parts = explode('_', $muid, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$prefix, $body] = $parts;

        // Both parts must be non-empty
        if ($prefix === '' || $body === '') {
            return false;
        }

        return true;
    }

    /**
     * Validate a prefix against configuration rules.
     */
    public function isValidPrefix(string $prefix): bool
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

    /**
     * Validate the body format (characters only).
     */
    public function isValidBody(string $body): bool
    {
        // Check if body contains only valid base62 or base36 characters
        $encoder = $this->factory->getEncoder();

        return $encoder->isValid($body);
    }

    /**
     * Detect the strategy used for a MUID body.
     */
    public function detectStrategy(string $body): string
    {
        // If body is purely numeric, it's incremental or padded
        if (ctype_digit($body)) {
            // Check if it has leading zeros (padded)
            if (strlen($body) > 1 && $body[0] === '0') {
                return 'padded';
            }

            return 'incremental';
        }

        // Non-numeric bodies are ordered (time-sortable)
        return 'ordered';
    }

    /**
     * Validate multiple MUIDs at once.
     *
     * @param  array<string>  $muids  Array of MUIDs to validate
     * @param  string|null  $expectedPrefix  Expected prefix (optional)
     * @return array<string, bool> Map of MUID => is_valid
     */
    public function validateMany(array $muids, ?string $expectedPrefix = null): array
    {
        $results = [];

        foreach ($muids as $muid) {
            $results[$muid] = $this->validate($muid, $expectedPrefix);
        }

        return $results;
    }

    /**
     * Filter an array of MUIDs, returning only valid ones.
     *
     * @param  array<string>  $muids  Array of MUIDs to filter
     * @param  string|null  $expectedPrefix  Expected prefix (optional)
     * @return array<string> Array of valid MUIDs
     */
    public function filterValid(array $muids, ?string $expectedPrefix = null): array
    {
        return array_filter($muids, fn ($muid) => $this->validate($muid, $expectedPrefix));
    }
}
