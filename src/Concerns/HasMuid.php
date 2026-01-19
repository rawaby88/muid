<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Concerns;

use Rawaby88\Muid\Facades\Muid;

/**
 * Trait for Eloquent models that use MUID as primary key.
 *
 * @property string $id
 */
trait HasMuid
{
    /**
     * Boot the trait.
     */
    public static function bootHasMuid(): void
    {
        static::creating(function ($model) {
            foreach ($model->muidColumns() as $column) {
                if (empty($model->{$column})) {
                    $model->{$column} = $model->generateMuidForColumn($column);
                }
            }
        });
    }

    /**
     * Initialize the trait.
     */
    public function initializeHasMuid(): void
    {
        $this->usesUniqueIds = true;
    }

    /**
     * Get the prefix for MUID generation.
     * Override this method in your model to customize the prefix.
     */
    public function muidPrefix(): string
    {
        // Default to lowercase class basename (e.g., 'User' becomes 'user')
        return strtolower(class_basename($this));
    }

    /**
     * Get the strategy for MUID generation.
     * Override this method in your model to customize the strategy.
     */
    public function muidStrategy(): string
    {
        return config('muid.default_strategy', 'ordered');
    }

    /**
     * Get the maximum length for MUID generation.
     * Override this method in your model to customize the length.
     */
    public function muidMaxLength(): int
    {
        return config('muid.lengths.standard', 36);
    }

    /**
     * Get the primary key column name for MUID.
     * Override this method in your model to customize the column name.
     */
    public function muidColumn(): string
    {
        // Default to the model's primaryKey property
        return $this->primaryKey ?? 'id';
    }

    /**
     * Get the columns that should have MUIDs.
     * Override this method in your model to specify additional MUID columns.
     *
     * @return array<string>
     */
    public function muidColumns(): array
    {
        return [$this->muidColumn()];
    }

    /**
     * Get the primary key for the model.
     */
    public function getKeyName(): string
    {
        return $this->muidColumn();
    }

    /**
     * Generate a new MUID for a specific column.
     * Override this method to customize MUID generation per column.
     */
    public function generateMuidForColumn(string $column): string
    {
        return Muid::generate(
            $this->muidPrefixForColumn($column),
            $this->muidStrategyForColumn($column),
            $this->muidMaxLengthForColumn($column)
        );
    }

    /**
     * Get the prefix for a specific column.
     * Override this method to use different prefixes for different columns.
     */
    public function muidPrefixForColumn(string $column): string
    {
        return $this->muidPrefix();
    }

    /**
     * Get the strategy for a specific column.
     * Override this method to use different strategies for different columns.
     */
    public function muidStrategyForColumn(string $column): string
    {
        return $this->muidStrategy();
    }

    /**
     * Get the max length for a specific column.
     * Override this method to use different lengths for different columns.
     */
    public function muidMaxLengthForColumn(string $column): int
    {
        return $this->muidMaxLength();
    }

    /**
     * Generate a new MUID using the model's configuration.
     */
    public function newMuid(): string
    {
        return Muid::generate(
            $this->muidPrefix(),
            $this->muidStrategy(),
            $this->muidMaxLength()
        );
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Get whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Validate a MUID for this model.
     */
    public function isValidMuid(string $muid): bool
    {
        return Muid::isValid($muid, $this->muidPrefix(), $this->muidStrategy());
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Resolve the route binding query.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        // Validate MUID format before querying
        if ($field === $this->getKeyName() && ! Muid::isValid((string) $value)) {
            // Return a query that will not find anything
            return $query->whereRaw('1 = 0');
        }

        return $query->where($field, $value);
    }
}
