<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Feature;

use Abordage\EloquentPercentile\Tests\Models\ResponseLog;
use Abordage\EloquentPercentile\Tests\TestCase;
use Exception;

class MedianTest extends TestCase
{
    public function test_median(): void
    {
        $this->assertEqualsWithDelta(315.0, ResponseLog::median('response_time'), 0.001);
    }

    public function test_missing_column(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Undefined column/');

        ResponseLog::median('response_size');
    }
}
