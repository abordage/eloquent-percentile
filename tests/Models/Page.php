<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile\Tests\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Page
 *
 * @property int $id
 * @property string $name
 * @property-read Collection|ResponseLog[] $responseLogs
 *
 * @mixin Eloquent
 */
class Page extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function responseLogs(): HasMany
    {
        return $this->hasMany(ResponseLog::class);
    }
}
