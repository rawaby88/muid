<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rawaby88\Muid\MuidServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            MuidServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Muid' => \Rawaby88\Muid\Facades\Muid::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
