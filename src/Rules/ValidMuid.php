<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Rawaby88\Muid\Facades\Muid;

class ValidMuid implements ValidationRule
{
    /**
     * Create a new validation rule instance.
     *
     * @param  string|null  $prefix  Expected prefix for the MUID
     * @param  string|null  $strategy  Expected strategy for the MUID
     */
    public function __construct(
        protected ?string $prefix = null,
        protected ?string $strategy = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        if (! Muid::isValid($value, $this->prefix, $this->strategy)) {
            if ($this->prefix !== null) {
                $fail("The :attribute must be a valid MUID with prefix '{$this->prefix}'.");
            } else {
                $fail('The :attribute must be a valid MUID.');
            }
        }
    }

    /**
     * Create a rule that validates MUIDs with any prefix.
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Create a rule that validates MUIDs with a specific prefix.
     */
    public static function withPrefix(string $prefix): static
    {
        return new static($prefix);
    }

    /**
     * Create a rule that validates MUIDs with a specific strategy.
     */
    public static function withStrategy(string $strategy): static
    {
        return new static(null, $strategy);
    }

    /**
     * Create a rule that validates MUIDs with a specific prefix and strategy.
     */
    public static function for(string $prefix, string $strategy): static
    {
        return new static($prefix, $strategy);
    }
}
