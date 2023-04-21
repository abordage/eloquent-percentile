<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ResponseLog
 *
 * @property int $id
 * @property int $page_id
 * @property int|null $response_time
 * @property-read Page $page
 * @method static float|null percentile($columns, $percentile)
 * @method static float|null median($columns)
 */
class ResponseLog extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
