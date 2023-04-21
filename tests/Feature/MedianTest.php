<?php

/** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\ResponseLog;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;

/**
 * @covers \Abordage\EloquentPercentile\EloquentPercentileServiceProvider
 * @small
 */
class MedianTest extends TestCase
{
    public function testMedian(): void
    {
        $this->assertEqualsWithDelta(315.0, ResponseLog::median('response_time'), 0.001);
    }

    public function testMissingColumn(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        ResponseLog::median('response_size');
    }
}
