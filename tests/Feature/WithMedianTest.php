<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\Page;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;

class WithMedianTest extends TestCase
{
    public function test_percentile(): void
    {
        $page = Page::withMedian('responseLogs', 'response_time')->first();
        $this->assertEqualsWithDelta(315, $page->response_logs_median_response_time, 0.001);
    }

    public function test_missing_relation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Call to undefined method/');

        Page::withMedian('responseStats', 'response_time')->first();
    }

    public function test_missing_column(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        Page::withMedian('responseLogs', 'response_size')->first();
    }
}
