<?php

declare(strict_types=1);

namespace Rawaby88\Muid;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;
use Rawaby88\Muid\Support\Encoder;
use Rawaby88\Muid\Support\MuidFactory;

class MuidServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/muid.php', 'muid');

        $this->app->singleton(Encoder::class, function ($app) {
            return new Encoder(config('muid.encoding.type'));
        });

        $this->app->singleton(MuidFactory::class, function ($app) {
            return new MuidFactory($app->make(Encoder::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/muid.php' => config_path('muid.php'),
        ], 'muid-config');

        $this->registerStandardMuidMacros();
        $this->registerSmallMuidMacros();
        $this->registerTinyMuidMacros();
        $this->registerIntegerMuidMacros();
    }

    /**
     * Register Standard MUID Blueprint macros (36 chars).
     */
    protected function registerStandardMuidMacros(): void
    {
        /**
         * Create a new muid column on the table.
         */
        Blueprint::macro('muid', function (string $column = 'id') {
            /** @var Blueprint $this */
            $length = config('muid.lengths.standard', 36);

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new muid column as the primary key for the table.
         */
        Blueprint::macro('primaryMuid', function (string $column = 'id', ?string $name = null, ?string $algorithm = null): Fluent {
            /** @var Blueprint $this */
            $length = config('muid.lengths.standard', 36);
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new muid column on the table with a foreign key constraint.
         */
        Blueprint::macro('foreignMuid', function (string $column): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.lengths.standard', 36),
            ]));
        });

        /**
         * Create a nullable muid column on the table.
         */
        Blueprint::macro('nullableMuid', function (string $column) {
            /** @var Blueprint $this */
            return $this->muid($column)->nullable();
        });

        /**
         * Add the proper columns for a polymorphic table using MUIDs.
         */
        Blueprint::macro('muidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type");
            $this->muid("{$name}_id");
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using MUIDs.
         */
        Blueprint::macro('nullableMuidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type")->nullable();
            $this->muid("{$name}_id")->nullable();
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
    }

    /**
     * Register Small MUID Blueprint macros (24 chars).
     */
    protected function registerSmallMuidMacros(): void
    {
        /**
         * Create a new smallMuid column on the table.
         */
        Blueprint::macro('smallMuid', function (string $column = 'id') {
            /** @var Blueprint $this */
            $length = config('muid.lengths.small', 24);

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new smallMuid column as the primary key for the table.
         */
        Blueprint::macro('primarySmallMuid', function (string $column = 'id', ?string $name = null, ?string $algorithm = null): Fluent {
            /** @var Blueprint $this */
            $length = config('muid.lengths.small', 24);
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new smallMuid column on the table with a foreign key constraint.
         */
        Blueprint::macro('foreignSmallMuid', function (string $column): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.lengths.small', 24),
            ]));
        });

        /**
         * Create a nullable smallMuid column on the table.
         */
        Blueprint::macro('nullableSmallMuid', function (string $column) {
            /** @var Blueprint $this */
            return $this->smallMuid($column)->nullable();
        });

        /**
         * Add the proper columns for a polymorphic table using small MUIDs.
         */
        Blueprint::macro('muidSmallMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type");
            $this->smallMuid("{$name}_id");
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using small MUIDs.
         */
        Blueprint::macro('nullableSmallMuidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type")->nullable();
            $this->smallMuid("{$name}_id")->nullable();
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
    }

    /**
     * Register Tiny MUID Blueprint macros (16 chars).
     */
    protected function registerTinyMuidMacros(): void
    {
        /**
         * Create a new tinyMuid column on the table.
         */
        Blueprint::macro('tinyMuid', function (string $column = 'id') {
            /** @var Blueprint $this */
            $length = config('muid.lengths.tiny', 16);

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new tinyMuid column as the primary key for the table.
         */
        Blueprint::macro('primaryTinyMuid', function (string $column = 'id', ?string $name = null, ?string $algorithm = null): Fluent {
            /** @var Blueprint $this */
            $length = config('muid.lengths.tiny', 16);
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new tinyMuid column on the table with a foreign key constraint.
         */
        Blueprint::macro('foreignTinyMuid', function (string $column): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.lengths.tiny', 16),
            ]));
        });

        /**
         * Create a nullable tinyMuid column on the table.
         */
        Blueprint::macro('nullableTinyMuid', function (string $column) {
            /** @var Blueprint $this */
            return $this->tinyMuid($column)->nullable();
        });

        /**
         * Add the proper columns for a polymorphic table using tiny MUIDs.
         */
        Blueprint::macro('muidTinyMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type");
            $this->tinyMuid("{$name}_id");
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using tiny MUIDs.
         */
        Blueprint::macro('nullableTinyMuidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type")->nullable();
            $this->tinyMuid("{$name}_id")->nullable();
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
    }

    /**
     * Register Integer MUID Blueprint macros (for incremental/padded strategies).
     */
    protected function registerIntegerMuidMacros(): void
    {
        /**
         * Create a new integer muid column on the table.
         */
        Blueprint::macro('integerMuid', function (string $column = 'id') {
            /** @var Blueprint $this */
            return $this->unsignedBigInteger($column);
        });

        /**
         * Create a new integer muid column as the primary key (auto-increment).
         */
        Blueprint::macro('primaryIntegerMuid', function (string $column = 'id') {
            /** @var Blueprint $this */
            return $this->id($column);
        });

        /**
         * Create a new integer muid column with a foreign key constraint.
         */
        Blueprint::macro('foreignIntegerMuid', function (string $column): ForeignIdColumnDefinition {
            /** @var Blueprint $this */
            return $this->foreignId($column);
        });

        /**
         * Create a nullable integer muid column on the table.
         */
        Blueprint::macro('nullableIntegerMuid', function (string $column) {
            /** @var Blueprint $this */
            return $this->unsignedBigInteger($column)->nullable();
        });

        /**
         * Add the proper columns for a polymorphic table using integer MUIDs.
         */
        Blueprint::macro('integerMuidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type");
            $this->unsignedBigInteger("{$name}_id");
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using integer MUIDs.
         */
        Blueprint::macro('nullableIntegerMuidMorphs', function (string $name, ?string $indexName = null): void {
            /** @var Blueprint $this */
            $this->string("{$name}_type")->nullable();
            $this->unsignedBigInteger("{$name}_id")->nullable();
            $this->index(["{$name}_type", "{$name}_id"], $indexName);
        });
    }
}
