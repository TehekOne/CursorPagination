<?php

namespace TehekOne\CursorPagination;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
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

            $query_orders = isset($this->query)
                ? collect($this->query->orders)
                : collect($this->orders);

            $identifierSort = null;

            if ($query_orders->isNotEmpty()) {
                $identifierSort = $query_orders->first();
                $options['identifier'] = $identifierSort['column'];
            } else {
                $options['identifier'] = isset($this->model) ? $this->model->getKeyName() : 'id';
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
    }
}
