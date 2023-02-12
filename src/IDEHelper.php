<?php

declare(strict_types=1);

namespace {
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    class Eloquent extends Model
    {
        /**
         * Retrieve the "percentile" result of the query.
         *
         * @param string|string[] $columns
         * @param float|int|string $percentile
         * @return float|null
         * @static
         */
        public static function percentile($columns, $percentile): ?float
        {
            /** @var \Illuminate\Database\Query\Builder $instance */
            return $instance->percentile($columns, $percentile);
        }

        /**
         * Retrieve the "median" result of the query.
         *
         * @param string|string[] $columns
         * @return float|null
         * @static
         */
        public static function median($columns): ?float
        {
            /** @var \Illuminate\Database\Query\Builder $instance */
            return $instance->median($columns);
        }

        /**
         * Add sub select queries to include the percentile of the relation's column.
         *
         * @param string|array $relation
         * @param string $column
         * @param float|int|string $percentile
         * @return Eloquent|Builder
         * @static
         */
        public static function withPercentile($relation, string $column, $percentile): Builder
        {
            /** @var Builder $instance */
            return $instance->withPercentile($relation, $column, $percentile);
        }

        /**
         * Add sub select queries to include the median of the relation's column.
         *
         * @param string|array $relation
         * @param string $column
         * @return Eloquent|Builder
         * @static
         */
        public static function withMedian($relation, string $column): Builder
        {
            /** @var Builder $instance */
            return $instance->withMedian($relation, $column);
        }
    }
}
