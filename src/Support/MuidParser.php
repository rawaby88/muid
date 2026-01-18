<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Support;

class MuidParser
{
    protected MuidFactory $factory;

    public function __construct(MuidFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Parse a MUID into its components.
     *
     * @param  string  $muid  The MUID to parse
     * @param  string|null  $strategy  Strategy to use for parsing (optional, auto-detects if null)
     */
    public function parse(string $muid, ?string $strategy = null): ?MuidComponents
    {
        // Basic format validation
        if (! $this->hasValidFormat($muid)) {
            return null;
        }

        $parts = explode('_', $muid, 2);
        [$prefix, $body] = $parts;

        // Validate prefix format
        if (! $this->isValidPrefix($prefix)) {
            return null;
        }

        // If no strategy specified, try to detect it
        if ($strategy === null) {
            $strategy = $this->detectStrategy($body);
        }

        // Parse using the appropriate generator
        $generator = $this->factory->getGenerator($strategy);

        return $generator->parse($muid);
    }

    /**
     * Extract the prefix from a MUID without full parsing.
     */
    public function extractPrefix(string $muid): ?string
    {
        if (! $this->hasValidFormat($muid)) {
            return null;
        }

        $parts = explode('_', $muid, 2);

        return $parts[0];
    }

    /**
     * Extract the body from a MUID without full parsing.
     */
    public function extractBody(string $muid): ?string
    {
        if (! $this->hasValidFormat($muid)) {
            return null;
        }

        $parts = explode('_', $muid, 2);

        return $parts[1];
    }

    /**
     * Parse multiple MUIDs at once.
     *
     * @param  array<string>  $muids  Array of MUIDs to parse
     * @param  string|null  $strategy  Strategy to use for parsing (optional)
     * @return array<string, MuidComponents|null> Map of MUID => components
     */
    public function parseMany(array $muids, ?string $strategy = null): array
    {
        $results = [];

        foreach ($muids as $muid) {
            $results[$muid] = $this->parse($muid, $strategy);
        }

        return $results;
    }

    /**
     * Check if the MUID has a valid basic format.
     */
    protected function hasValidFormat(string $muid): bool
    {
        if (substr_count($muid, '_') !== 1) {
            return false;
        }

        $parts = explode('_', $muid, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$prefix, $body] = $parts;

        return $prefix !== '' && $body !== '';
    }

    /**
     * Validate a prefix against configuration rules.
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

    /**
     * Detect the strategy used for a MUID body.
     */
    protected function detectStrategy(string $body): string
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
}
