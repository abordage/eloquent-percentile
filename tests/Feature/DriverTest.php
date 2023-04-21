<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\Page;
use Abordage\EloquentPercentile\Tests\Models\ResponseLog;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;

/**
 * @covers \Abordage\EloquentPercentile\EloquentPercentileServiceProvider
 * @small
 */
class DriverTest extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
    }

    public function testPercentileDriverIsNotPgsql(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Driver mysql not supported/');

        ResponseLog::percentile('response_time', 0.95);
    }

    public function testWithMedianDriverIsNotPgsql(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Driver mysql not supported/');

        Page::withMedian('responseLogs', 'response_time')->first();
    }
}
