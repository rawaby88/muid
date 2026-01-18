<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Contracts;

use Rawaby88\Muid\Support\MuidComponents;

interface MuidGenerator
{
    /**
     * Generate a new MUID with the given prefix.
     *
     * @param  string  $prefix  The prefix for the MUID (e.g., 'usr', 'cus')
     * @param  int|null  $maxLength  Maximum length for the MUID (null uses default)
     */
    public function generate(string $prefix, ?int $maxLength = null): string;

    /**
     * Validate if a MUID is valid.
     *
     * @param  string  $muid  The MUID to validate
     * @param  string|null  $expectedPrefix  Optional expected prefix to validate against
     */
    public function validate(string $muid, ?string $expectedPrefix = null): bool;

    /**
     * Parse a MUID into its components.
     *
     * @param  string  $muid  The MUID to parse
     * @return MuidComponents|null Returns null if parsing fails
     */
    public function parse(string $muid): ?MuidComponents;

    /**
     * Get the strategy name for this generator.
     */
    public function getStrategy(): string;
}
