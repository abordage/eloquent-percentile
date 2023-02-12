<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ResponseLog
 *
 * @property int $id
 * @property int $page_id
 * @property int|null $response_time
 * @property-read Page $page
 *
 * @mixin Eloquent
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
