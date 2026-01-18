<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Support;

use InvalidArgumentException;
use Rawaby88\Muid\Contracts\MuidGenerator;
use Rawaby88\Muid\Generators\IncrementalGenerator;
use Rawaby88\Muid\Generators\OrderedGenerator;
use Rawaby88\Muid\Generators\PaddedIncrementalGenerator;

class MuidFactory
{
    /**
     * Cache of generator instances.
     *
     * @var array<string, MuidGenerator>
     */
    protected array $generators = [];

    protected Encoder $encoder;

    protected MuidValidator $validator;

    protected MuidParser $parser;

    public function __construct(?Encoder $encoder = null)
    {
        $this->encoder = $encoder ?? new Encoder;
        $this->validator = new MuidValidator($this);
        $this->parser = new MuidParser($this);
    }

    /**
     * Generate a new MUID.
     *
     * @param  string  $prefix  The prefix for the MUID
     * @param  string|null  $strategy  The generation strategy (null uses default)
     * @param  int|null  $maxLength  Maximum length (null uses default)
     */
    public function generate(string $prefix, ?string $strategy = null, ?int $maxLength = null): string
    {
        $this->validatePrefix($prefix);

        $strategy ??= config('muid.default_strategy', 'ordered');
        $generator = $this->getGenerator($strategy);

        return $generator->generate($prefix, $maxLength);
    }

    /**
     * Generate a MUID with standard length (36 chars).
     */
    public function standard(string $prefix, ?string $strategy = null): string
    {
        return $this->generate($prefix, $strategy, config('muid.lengths.standard', 36));
    }

    /**
     * Generate a MUID with small length (24 chars).
     */
    public function small(string $prefix, ?string $strategy = null): string
    {
        return $this->generate($prefix, $strategy, config('muid.lengths.small', 24));
    }

    /**
     * Generate a MUID with tiny length (16 chars).
     */
    public function tiny(string $prefix, ?string $strategy = null): string
    {
        return $this->generate($prefix, $strategy, config('muid.lengths.tiny', 16));
    }

    /**
     * Validate a MUID.
     *
     * @param  string  $muid  The MUID to validate
     * @param  string|null  $expectedPrefix  Expected prefix (optional)
     * @param  string|null  $strategy  Strategy to use for validation (optional)
     */
    public function isValid(string $muid, ?string $expectedPrefix = null, ?string $strategy = null): bool
    {
        return $this->validator->validate($muid, $expectedPrefix, $strategy);
    }

    /**
     * Parse a MUID into its components.
     *
     * @param  string  $muid  The MUID to parse
     * @param  string|null  $strategy  Strategy to use for parsing (optional, auto-detects if null)
     */
    public function parse(string $muid, ?string $strategy = null): ?MuidComponents
    {
        return $this->parser->parse($muid, $strategy);
    }

    /**
     * Extract the prefix from a MUID.
     */
    public function extractPrefix(string $muid): ?string
    {
        $parts = explode('_', $muid, 2);

        if (count($parts) !== 2) {
            return null;
        }

        return $parts[0];
    }

    /**
     * Extract the body from a MUID.
     */
    public function extractBody(string $muid): ?string
    {
        $parts = explode('_', $muid, 2);

        if (count($parts) !== 2) {
            return null;
        }

        return $parts[1];
    }

    /**
     * Get a generator by strategy name.
     */
    public function getGenerator(string $strategy): MuidGenerator
    {
        if (! isset($this->generators[$strategy])) {
            $this->generators[$strategy] = $this->createGenerator($strategy);
        }

        return $this->generators[$strategy];
    }

    /**
     * Get all available strategies.
     *
     * @return array<string>
     */
    public function getAvailableStrategies(): array
    {
        return ['ordered', 'incremental', 'padded'];
    }

    /**
     * Get the encoder instance.
     */
    public function getEncoder(): Encoder
    {
        return $this->encoder;
    }

    /**
     * Get the validator instance.
     */
    public function getValidator(): MuidValidator
    {
        return $this->validator;
    }

    /**
     * Get the parser instance.
     */
    public function getParser(): MuidParser
    {
        return $this->parser;
    }

    /**
     * Create a generator instance for the given strategy.
     */
    protected function createGenerator(string $strategy): MuidGenerator
    {
        return match ($strategy) {
            'ordered' => new OrderedGenerator($this->encoder),
            'incremental' => new IncrementalGenerator,
            'padded' => new PaddedIncrementalGenerator,
            default => throw new InvalidArgumentException("Unknown strategy: {$strategy}. Available: ordered, incremental, padded"),
        };
    }

    /**
     * Validate a prefix against configuration rules.
     *
     * @throws InvalidArgumentException
     */
    protected function validatePrefix(string $prefix): void
    {
        $minLength = config('muid.prefix.min_length', 2);
        $maxLength = config('muid.prefix.max_length', 8);
        $pattern = config('muid.prefix.pattern', '/^[a-z][a-z0-9]*$/i');

        $length = strlen($prefix);

        if ($length < $minLength) {
            throw new InvalidArgumentException(
                "Prefix '{$prefix}' is too short. Minimum length is {$minLength} characters."
            );
        }

        if ($length > $maxLength) {
            throw new InvalidArgumentException(
                "Prefix '{$prefix}' is too long. Maximum length is {$maxLength} characters."
            );
        }

        if (preg_match($pattern, $prefix) !== 1) {
            throw new InvalidArgumentException(
                "Prefix '{$prefix}' does not match the required pattern. It must start with a letter and contain only alphanumeric characters."
            );
        }
    }
}
