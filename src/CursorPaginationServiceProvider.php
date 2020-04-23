<?php

namespace TehekOne\CursorPagination;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;
use TehekOne\CursorPagination\Pagination\Paginator;

/**
 * Class CursorPaginationServiceProvider
 *
 * @package TehekOne\CursorPagination
 */
class CursorPaginationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * Cursor paginator implementation.
         *
         * @param null $limit
         * @param array $columns
         *
         * @return Paginator
         */
        $macro = function ($limit = null, $columns = ['*']) {
            $options = [];

            $builder = $this;

            if ($this instanceof BelongsToMany || $this instanceof HasManyThrough) {
                $builder = $this->getQuery();
            }

            $query_orders = method_exists($builder, 'getQuery')
                ? collect($builder->getQuery()->orders)
                : collect($builder->orders);

            $identifierSort = null;

            if ($query_orders->isNotEmpty()) {
                $identifierSort = $query_orders->first();
                $options['identifier'] = $identifierSort['column'];
            } else {
                $options['identifier'] = method_exists($builder,
                    'getModel') ? $builder->getModel()->getKeyName() : 'id';
            }

            $cursor = Paginator::resolve();

            $identifierSortInverted = $identifierSort ? $identifierSort['direction'] === 'desc' : false;

            if ($limit === null) {
                $limit = request()->input('limit');
            }

            if ($cursor->isBefore()) {
                $this->where($options['identifier'], $identifierSortInverted ? '>' : '<', $cursor->beforeCursor());
            }

            if ($cursor->isAfter()) {
                $this->where($options['identifier'], $identifierSortInverted ? '<' : '>', $cursor->afterCursor());
            }

            $this->take($limit);

            return new Paginator($this->get($columns), $limit, $options);
        };

        EloquentBuilder::macro('cursorPaginate', $macro);
        QueryBuilder::macro('cursorPaginate', $macro);

        HasManyThrough::macro('cursorPaginate', $macro);
        BelongsToMany::macro('cursorPaginate', $macro);
    }
}
