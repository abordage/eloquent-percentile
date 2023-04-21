<?php

/** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\Page;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;
use InvalidArgumentException;

/**
 * @covers \Abordage\EloquentPercentile\EloquentPercentileServiceProvider
 * @small
 */
class WithPercentileTest extends TestCase
{
    public function testPercentile(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0.65)->first();

        $this->assertEqualsWithDelta(601.25, $page->response_logs_percentile65_response_time, 0.001);
    }

    public function testPercentileWithCondition(): void
    {
        $page = Page::withPercentile(['responseLogs' => fn($q) => $q->where('is_active', true)], 'response_time', 0.65)
            ->first();

        $this->assertEqualsWithDelta(591.5, $page->response_logs_percentile65_response_time, 0.001);
    }

    public function testPercentileAsCustomName(): void
    {
        $page = Page::withPercentile('responseLogs as percentile_result', 'response_time', 0.65)->first();
        $this->assertEqualsWithDelta(601.25, $page->percentile_result, 0.001);
    }

    public function testPercentile50(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0.5)->first();
        $this->assertEqualsWithDelta(315, $page->response_logs_percentile50_response_time, 0.001);
    }

    public function testZero(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 0)->first();
        $this->assertEqualsWithDelta(2.0, $page->response_logs_percentile0_response_time, 0.001);
    }

    public function testOne(): void
    {
        $page = Page::withPercentile('responseLogs', 'response_time', 1)->first();
        $this->assertEqualsWithDelta(85677.0, $page->response_logs_percentile100_response_time, 0.001);
    }

    public function testMissingRelation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Call to undefined method/');

        Page::withPercentile('responseStats', 'response_time', 0.95)->first();
    }

    public function testEmptyRelation(): void
    {
        $page = Page::withPercentile('emptyResponseLogs', 'response_time', 0.65)->first();
        $this->assertNull($page->response_logs_percentile65_response_time);
    }

//    public function test_wrong_type_argument_relation(): void
//    {
//        $this->expectException(Exception::class);
//        $this->expectExceptionMessageMatches('/must be a string/');
//
//        Page::withPercentile(123, 'response_size', 0.95)->first();
//    }
//
//    public function test_wrong_type_argument_column(): void
//    {
//        $this->expectException(Exception::class);
//        $this->expectExceptionMessageMatches('/must be a string/');
//
//        Page::withPercentile('responseLogs', 123, 0.95)->first();
//    }

    public function testMissingColumn(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        Page::withPercentile('responseLogs', 'response_size', 0.95)->first();
    }

    public function testPercentileOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not between 0 and 1');

        Page::withPercentile('responseLogs', 'response_time', 1.1)->first();
    }

    public function testPercentileNonNumeric(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not numeric');

        Page::withPercentile('responseLogs', 'response_time', 'test')->first();
    }
}
