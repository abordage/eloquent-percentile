<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\Page;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;
use InvalidArgumentException;

class WithPercentileTest extends TestCase
{
    public function test_percentile(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0.65)->first();
        $this->assertEqualsWithDelta(601.25, $page->response_logs_percentile65_response_time, 0.001);
    }

    public function test_percentile50(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0.5)->first();
        $this->assertEqualsWithDelta(315, $page->response_logs_percentile50_response_time, 0.001);
    }

    public function test_zero(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0)->first();
        $this->assertEqualsWithDelta(2.0, $page->response_logs_percentile0_response_time, 0.001);
    }

    public function test_one(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 1)->first();
        $this->assertEqualsWithDelta(85677.0, $page->response_logs_percentile100_response_time, 0.001);
    }

    public function test_missing_relation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Call to undefined method/');

        Page::withPercentile('responseStats', 'response_time', 0.95)->first();
    }

    public function test_missing_column(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        Page::withPercentile('responseLogs', 'response_size', 0.95)->first();
    }

    public function test_percentile_out_of_range(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not between 0 and 1');

        Page::withPercentile('responseLogs', 'response_time', 1.1)->first();
    }

    public function test_percentile_non_numeric(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not numeric');

        Page::withPercentile('responseLogs', 'response_time', 'test')->first();
    }
}
