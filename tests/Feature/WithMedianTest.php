<?php

/** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\Page;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;

/**
 * @covers \Abordage\EloquentPercentile\EloquentPercentileServiceProvider
 * @small
 */
class WithMedianTest extends TestCase
{
    public function testPercentile(): void
    {
        $page = Page::withMedian('responseLogs', 'response_time')->first();
        $this->assertEqualsWithDelta(315, $page->response_logs_median_response_time, 0.001);
    }

    public function testMissingRelation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Call to undefined method/');

        Page::withMedian('responseStats', 'response_time')->first();
    }

    public function testEmptyRelation(): void
    {
        $page = Page::withMedian('emptyResponseLogs', 'response_time')->first();
        $this->assertNull($page->response_logs_median_response_time);
    }

    public function testMissingColumn(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        Page::withMedian('responseLogs', 'response_size')->first();
    }
}
