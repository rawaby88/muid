<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait for Eloquent models that use integer-based MUID with virtual prefix.
 *
 * The database stores a BIGINT auto-increment value, but the model
 * presents it with a prefix (e.g., 42 becomes "inv_42" or "inv_0000042").
 *
 * @property int $id The raw integer ID from database
 *
 * @mixin Model
 */
trait HasIntegerMuid
{
    /**
     * Get the prefix for MUID generation.
     * Override this method in your model to customize the prefix.
     */
    public function muidPrefix(): string
    {
        return strtolower(class_basename($this));
    }

    /**
     * Get the strategy for MUID formatting.
     * Override this method: 'incremental' for "inv_42" or 'padded' for "inv_0000042"
     */
    public function muidStrategy(): string
    {
        return 'incremental';
    }

    /**
     * Get the padding length for padded strategy.
     * Override this method to customize padding length.
     */
    public function muidPaddingLength(): int
    {
        return config('muid.incremental.padding_length', 7);
    }

    /**
     * Get the MUID string representation of the ID.
     * This is the accessor that transforms the integer to prefixed string.
     */
    public function getMuidAttribute(): string
    {
        return $this->formatMuid($this->getRawId());
    }

    /**
     * Get the raw integer ID from the database.
     */
    public function getRawId(): ?int
    {
        $value = $this->attributes[$this->getKeyName()] ?? null;

        return $value !== null ? (int) $value : null;
    }

    /**
     * Format an integer ID as a MUID string.
     */
    public function formatMuid(?int $id): ?string
    {
        if ($id === null) {
            return null;
        }

        $prefix = $this->muidPrefix();

        if ($this->muidStrategy() === 'padded') {
            return $prefix . '_' . str_pad((string) $id, $this->muidPaddingLength(), '0', STR_PAD_LEFT);
        }

        return $prefix . '_' . $id;
    }

    /**
     * Parse a MUID string to extract the integer ID.
     */
    public function parseMuid(string $muid): ?int
    {
        $prefix = $this->muidPrefix();
        $expectedPrefix = $prefix . '_';

        if (!str_starts_with($muid, $expectedPrefix)) {
            return null;
        }

        $body = substr($muid, strlen($expectedPrefix));

        if (!ctype_digit($body)) {
            return null;
        }

        return (int) $body;
    }

    /**
     * Validate if a MUID string is valid for this model.
     */
    public function isValidMuid(string $muid): bool
    {
        return $this->parseMuid($muid) !== null;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        // If binding by primary key, parse the MUID to get the integer
        if ($field === $this->getKeyName()) {
            $id = is_numeric($value) ? (int) $value : $this->parseMuid((string) $value);

            if ($id === null) {
                return null;
            }

            return $this->where($field, $id)->first();
        }

        return $this->where($field, $value)->first();
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        // Return the MUID string for URL generation
        return $this->muid;
    }

    /**
     * Convert the model to an array.
     * Includes the formatted MUID.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        // Add the formatted MUID to the array output
        $array['muid'] = $this->muid;

        return $array;
    }

    /**
     * Find a model by its MUID.
     *
     * @param  string  $muid
     * @return static|null
     */
    public static function findByMuid(string $muid): ?static
    {
        $instance = new static;
        $id = $instance->parseMuid($muid);

        if ($id === null) {
            return null;
        }

        return static::find($id);
    }

    /**
     * Find a model by its MUID or throw an exception.
     *
     * @param  string  $muid
     * @return static
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByMuidOrFail(string $muid): static
    {
        $instance = new static;
        $id = $instance->parseMuid($muid);

        if ($id === null) {
            $class = static::class;
            throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel($class, [$muid]);
        }

        return static::findOrFail($id);
    }
}
