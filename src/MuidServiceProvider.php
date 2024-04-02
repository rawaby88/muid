<?php

namespace Rawaby88\Muid;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;
use Rawaby88\Muid\Console\MuidModelCommand;

class MuidServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the muid services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/muid.php' => config_path('muid.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                MuidModelCommand::class,
            ]);
        }

        $this->bootTinyMuid();
        $this->bootSmallMuid();
        $this->bootStandardMuid();
    }

    private function bootTinyMuid()
    {
        /**
         * Create a new tinyMuid column as the primary key(s) for the table.
         *
         * @param  string|array  $columns
         * @param  string|null  $name
         * @param  string|null  $algorithm
         * @return Fluent
         */
        Blueprint::macro('primaryTinyMuid', function ($column = 'muid', $name = null, $algorithm = null) {
            $length = config('muid.tiny_muid_length');
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new tinyMuid column on the table.
         *
         * @param  string  $column
         * @return ColumnDefinition
         */
        Blueprint::macro('tinyMuid', function ($column = 'muid') {
            $length = config('muid.tiny_muid_length');

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new tinyMuid column on the table with a foreign key constraint.
         *
         * @param  string  $column
         * @return ForeignIdColumnDefinition
         */
        Blueprint::macro('foreignTinyMuid', function ($column = 'muid') {
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.tiny_muid_length'),
            ]));
        });

        /**
         * Add the proper columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('muidTinyMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type");

            $this->tinyMuid("{$name}_id");

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('nullableTinyMuidMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type")
                ->nullable();

            $this->tinyMuid("{$name}_id")
                ->nullable();

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });
    }

    private function bootSmallMuid()
    {
        /**
         * Create a new smallMuid column as the primary key(s) for the table.
         *
         * @param  string|array  $columns
         * @param  string|null  $name
         * @param  string|null  $algorithm
         * @return Fluent
         */
        Blueprint::macro('primarySmallMuid', function ($column = 'muid', $name = null, $algorithm = null) {
            $length = config('muid.small_muid_length');
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new smallMuid column on the table.
         *
         * @param  string  $column
         * @return ColumnDefinition
         */
        Blueprint::macro('smallMuid', function ($column = 'muid') {
            $length = config('muid.small_muid_length');

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new smallMuid column on the table with a foreign key constraint.
         *
         * @param  string  $column
         * @return ForeignIdColumnDefinition
         */
        Blueprint::macro('foreignSmallMuid', function ($column = 'muid') {
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.small_muid_length'),
            ]));
        });

        /**
         * Add the proper columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('muidSmallMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type");

            $this->smallMuid("{$name}_id");

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('nullableSmallMuidMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type")
                ->nullable();

            $this->smallMuid("{$name}_id")
                ->nullable();

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });
    }

    private function bootStandardMuid()
    {
        /**
         * Create a new muid column as the primary key(s) for the table.
         *
         * @param  string|array  $columns
         * @param  string|null  $name
         * @param  string|null  $algorithm
         * @return Fluent
         */
        Blueprint::macro('primaryMuid', function ($column = 'muid', $name = null, $algorithm = null) {
            $length = config('muid.muid_length');
            $this->addColumn('string', $column, compact('length'));

            return $this->primary($column, $name, $algorithm);
        });

        /**
         * Create a new muid column on the table.
         *
         * @param  string  $column
         * @return ColumnDefinition
         */
        Blueprint::macro('muid', function ($column = 'muid') {
            $length = config('muid.muid_length');

            return $this->addColumn('string', $column, compact('length'));
        });

        /**
         * Create a new muid column on the table with a foreign key constraint.
         *
         * @param  string  $column
         * @return ForeignIdColumnDefinition
         */
        Blueprint::macro('foreignMuid', function ($column = 'muid') {
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => config('muid.muid_length'),
            ]));
        });

        /**
         * Add the proper columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('muidMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type");

            $this->muid("{$name}_id");

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });

        /**
         * Add nullable columns for a polymorphic table using MUIDs.
         *
         * @param  string  $name
         * @param  string|null  $indexName
         * @return void
         */
        Blueprint::macro('nullableMuidMorphs', function ($name, $indexName = null) {
            $this->string("{$name}_type")
                ->nullable();

            $this->muid("{$name}_id")
                ->nullable();

            $this->index([
                "{$name}_type",
                "{$name}_id",
            ], $indexName);
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/muid.php', 'muid');

        // Register the main class to use with the facade
        $this->app->singleton('muid', function () {
            return new MuidService;
        });
    }
}
