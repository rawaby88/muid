<?php

namespace Rawaby88\Muid\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Rawaby88\Muid\MuidServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /**
     * model_with_muid_test
     * model_with_primaryMuid_test
     * model_with_foreignMuid_test
     * model_with_muidMorph_test
     * model_with_nullableMuidMorphs_test
     * model_without_muid_test
     */
    protected function setUpDatabase(): void
    {
        Schema::create('model_with_primaryMuid_test', function (Blueprint $table): void {
            $table->primaryMuid('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('model_with_muid_test', function (Blueprint $table): void {
            $table->muid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('model_with_foreignMuid_test', function (Blueprint $table): void {
            $table->muid('id')->primary();
            $table->foreignMuid('model_with_muid_test_id')->constrained('model_with_muid_test');
            $table->timestamps();
        });

        Schema::create('model_with_muidMorph_test', function (Blueprint $table): void {
            $table->muid('id')->primary();
            $table->muidMorphs('testable');
            $table->timestamps();
        });

        Schema::create('model_with_nullableMuidMorphs_test', function (Blueprint $table): void {
            $table->muid('id')
                ->primary();
            $table->nullableMuidMorphs('testable');
            $table->timestamps();
        });

        Schema::create('model_without_muid_test', function (Blueprint $table): void {
            $table->primaryMuid('id');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [MuidServiceProvider::class];
    }
}
