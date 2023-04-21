<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Page
 *
 * @property int $id
 * @property string $name
 * @property-read Collection|ResponseLog[] $responseLogs
 * @method static Builder withPercentile($relation, string $column, $percentile)
 * @method static Builder withMedian($relation, string $column)
 */
class Page extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function responseLogs(): HasMany
    {
        return $this->hasMany(ResponseLog::class);
    }

    public function emptyResponseLogs(): HasMany
    {
        return $this->responseLogs()->where('response_time', '>', 100000);
    }
}
