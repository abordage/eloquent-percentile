<?php

declare(strict_types=1);

namespace Abordage\EloquentPercentile;

use Eloquent;
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
        /**
         * @return Eloquent|Builder
         */
        Builder::macro('withPercentile', function ($relations, $column, $percentile) {
            if ($this->getConnection()->getDriverName() !== 'pgsql') {
                throw new Exception('Driver ' . $this->getConnection()->getDriverName() . ' not supported');
            }

            if (!is_numeric($percentile)) {
                throw new InvalidArgumentException('percentile is not numeric');
            }

            if ($percentile < 0 || $percentile > 1) {
                throw new InvalidArgumentException('percentile is not between 0 and 1');
            }

            if (empty($relations)) {
                return $this;
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

                $hashedColumn = $this->getQuery()->from === $relation->getQuery()->getQuery()->from
                    ? "{$relation->getRelationCountHash(false)}.$column"
                    : $column;

                $wrappedColumn = $this->getQuery()->getGrammar()->wrap(
                    $column === '*' ? $column : $relation->getRelated()->qualifyColumn($hashedColumn)
                );

                $expression = sprintf('percentile_cont(%s) within group (order by %s)', $percentile, $wrappedColumn);

                $query = $relation->getRelationExistenceQuery(
                    $relation->getRelated()->newQuery(),
                    $this,
                    new Expression($expression)
                )->setBindings([], 'select');

                $query->callScope($constraints);

                $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

                $query->orders = null;
                $query->setBindings([], 'order');

                if (count($query->columns) > 1) {
                    $query->columns = [$query->columns[0]];
                    $query->bindings['select'] = [];
                }

                $alias = $alias ?? Str::snake(
                    (string)preg_replace(
                        '/[^[:alnum:][:space:]_]/u',
                        '',
                        "$name percentile " . ($percentile * 100) . " $column"
                    )
                );

                if ($percentile * 100 == 50) {
                    $alias = Str::snake((string)preg_replace('/[^[:alnum:][:space:]_]/u', '', "$name median $column"));
                }

                $this->selectSub($query, $alias);
            }

            return $this;
        });

        /**
         * @return Eloquent|Builder
         */
        Builder::macro('withMedian', function ($relations, $column) {
            return $this->withPercentile($relations, $column, 0.5);
        });

        Builder::macro('percentile', function ($column, $percentile) {
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
            if (is_null($result)) {
                return null;
            }

            return floatval($result);
        });

        Builder::macro('median', function ($column) {
            return $this->percentile($column, 0.5);
        });
    }
}
