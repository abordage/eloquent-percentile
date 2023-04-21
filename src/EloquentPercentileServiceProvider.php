<?php

/**
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpReturnDocTypeMismatchInspection
 * @noinspection PhpDocMissingThrowsInspection
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

namespace Abordage\EloquentPercentile;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;

class EloquentPercentileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        static::percentileMacros();
    }

    public static function percentileMacros(): void
    {
        Builder::macro(
            'withPercentile', /** @return \Eloquent|\Illuminate\Database\Eloquent\Builder */
            function ($relations, $column, $percentile, $asMedian = false) {
                if ($this->getConnection()->getDriverName() !== 'pgsql') {
                    throw new Exception('Driver ' . $this->getConnection()->getDriverName() . ' not supported');
                }

                if (!is_numeric($percentile)) {
                    throw new InvalidArgumentException('percentile is not numeric');
                }

                if ($percentile < 0 || $percentile > 1) {
                    throw new InvalidArgumentException('percentile is not between 0 and 1');
                }

                if (is_null($this->query->columns)) {
                    $this->query->select([$this->query->from . '.*']);
                }

                $relations = is_array($relations) ? $relations : [$relations];

                foreach ($this->parseWithRelations($relations) as $name => $constraints) {
                    $segments = explode(' ', $name);

                    unset($alias);

                    if (count($segments) === 3 && Str::lower($segments[1]) === 'as') {
                        [$name, $alias] = [$segments[0], $segments[2]];
                    }

                    $relation = $this->getRelationWithoutConstraints($name);

                    $wrappedColumn = $this->getQuery()->getGrammar()->wrap($column);

                    $expression = sprintf(
                        'percentile_cont(%s) within group (order by %s)',
                        $percentile,
                        $wrappedColumn
                    );

                    $query = $relation->getRelationExistenceQuery(
                        $relation->getRelated()->newQuery(),
                        $this,
                        new Expression($expression)
                    )->setBindings([], 'select');

                    $query->callScope($constraints);

                    $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

                    $query->orders = null;
                    $query->setBindings([], 'order');

                    $alias = $alias ?? Str::snake(
                        (string)preg_replace(
                            '/[^[:alnum:][:space:]_]/u',
                            '',
                            "$name percentile " . ($percentile * 100) . " $column"
                        )
                    );

                    if ($percentile * 100 == 50 && $asMedian) {
                        $alias = Str::snake(
                            (string)preg_replace('/[^[:alnum:][:space:]_]/u', '', "$name median $column")
                        );
                    }

                    $this->selectSub($query, $alias);
                }

                return $this;
            }
        );

        Builder::macro('withMedian', /** @return \Eloquent|\Illuminate\Database\Eloquent\Builder */ function ($relations, $column) {
            return $this->withPercentile($relations, $column, 0.5, true);
        });

        Builder::macro('percentile', function ($column, $percentile): ?float {
            if ($this->getConnection()->getDriverName() !== 'pgsql') {
                throw new Exception('Driver ' . $this->getConnection()->getDriverName() . ' not supported');
            }

            if (!is_numeric($percentile)) {
                throw new InvalidArgumentException('percentile is not numeric');
            }

            if ($percentile < 0 || $percentile > 1) {
                throw new InvalidArgumentException('percentile is not between 0 and 1');
            }

            $wrappedColumn = $this->getGrammar()->wrap($column);

            $relation = $this->selectRaw(
                sprintf('percentile_cont(%s) within group (order by %s)', $percentile, $wrappedColumn)
            );
            $rows = $relation->get()->toArray();

            $result = array_values($rows[0])[0];

            return is_null($result) ? null : floatval($result);
        });

        Builder::macro('median', function ($column): ?float {
            return $this->percentile($column, 0.5);
        });
    }
}
