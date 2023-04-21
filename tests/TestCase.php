<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests;

use Abordage\EloquentPercentile\EloquentPercentileServiceProvider;
use Abordage\EloquentPercentile\Tests\Migration\Migration;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @coversNothing
 * @small
 */
class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EloquentPercentileServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']
            ->set('database.connections', [
                'postgresql' => [
                    'driver' => 'pgsql',
                    'host' => getenv('DB_HOST_POSTGRES'),
                    'port' => getenv('DB_PORT_POSTGRES'),
                    'database' => getenv('DB_DATABASE_POSTGRES'),
                    'username' => getenv('DB_USERNAME_POSTGRES'),
                    'password' => getenv('DB_PASSWORD_POSTGRES'),
                ],
            ]);

        $app['config']->set('database.default', 'postgresql');

        $migration = new Migration();
        $migration->up();
    }
}
