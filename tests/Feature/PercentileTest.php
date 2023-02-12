<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\ResponseLog;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;
use InvalidArgumentException;

class PercentileTest extends TestCase
{
    public function test_percentile(): void
    {
        $this->assertEqualsWithDelta(976.0, ResponseLog::percentile('response_time', 0.80), 0.001);
    }

    public function test_zero(): void
    {
        $this->assertEqualsWithDelta(2.0, ResponseLog::percentile('response_time', 0), 0.001);
    }

    public function test_one(): void
    {
        $this->assertEqualsWithDelta(85677.0, ResponseLog::percentile('response_time', 1), 0.001);
    }

    public function test_missing_column(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        ResponseLog::percentile('response_size', 0.95);
    }

    public function test_percentile_out_of_range(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not between 0 and 1');

        ResponseLog::percentile('response_time', 1.1);
    }

    public function test_percentile_non_numeric(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('percentile is not numeric');

        ResponseLog::percentile('response_time', 'test');
    }
}
