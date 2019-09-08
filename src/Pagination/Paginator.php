<?php

namespace TehekOne\LaravelCursorPagination\Pagination;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;

/**
 * Class Paginator
 *
 * @package TehekOne\LaravelCursorPagination\Pagination
 */
class Paginator extends AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Jsonable, PaginatorContract
{
    /**
     * @var string
     */
    protected $identifier = 'id';

    /**
     * @var Cursor
     */
    protected $cursor;

    /**
     * Create a new paginator instance.
     *
     * @param mixed $items
     * @param int $perPage
     * @param array $options
     */
    public function __construct($items, $perPage, $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;

        $this->cursor = static::resolve();

        $this->query = $this->getRawQuery();

        $this->path = $this->path !== '/'
            ? rtrim($this->path, '/')
            : rtrim(request()->path(), '/');

        $this->setItems($items);
    }

    /**
     * @return Cursor
     */
    public static function resolve(): Cursor
    {
        $request = request();

        $before = $request->input('before');
        $after = $request->input('after');

        return new Cursor($before, $after);
    }

    /**
     * Returns the request query without the cursor parameters.
     *
     * @return array
     */
    protected function getRawQuery()
    {
        return collect(request()->query())
            ->diffKeys([
                'before' => true,
                'after' => true,
            ])->all();
    }

    /**
     * Set the items for the paginator.
     *
     * @param mixed $items
     *
     * @return void
     */
    protected function setItems($items)
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);
        $this->items = $this->items->slice(0, $this->perPage);
    }

    /**
     * Determine if there is more items in the data store.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return true;
    }

    /**
     * Render the paginator using a given view.
     *
     * @param string|null $view
     * @param array $data
     *
     * @return string
     */
    public function render($view = null, $data = [])
    {
        return '';
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'paging' => [
                'cursor' => [
                    'before' => $this->previousCursor(),
                    'after' => $this->nextCursor(),
                ],
                'previous' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }

    /**
     * Get previous cursor.
     *
     * @return mixed|null
     */
    public function previousCursor()
    {
        return $this->firstItem()
            ? $this->cursor->encode($this->firstItem())
            : null;
    }

    /**
     * Return the first identifier of the results.
     *
     * @return mixed
     */
    public function firstItem()
    {
        return $this->getIdentifier($this->items->first());
    }

    /**
     * Gets identifier.
     *
     * @param $model
     *
     * @return mixed
     */
    protected function getIdentifier($model)
    {
        return $model ? $model->{$this->identifier} : null;
    }

    /**
     * Get next cursor.
     *
     * @return mixed|null
     */
    public function nextCursor()
    {
        return $this->lastItem()
            ? $this->cursor->encode($this->lastItem())
            : null;
    }

    /**
     * Return the last identifier of the results.
     *
     * @return mixed
     */
    public function lastItem()
    {
        return $this->getIdentifier($this->items->last());
    }

    /**
     * The URL for the previous page, or null.
     *
     * @return string|null
     */
    public function previousPageUrl()
    {
        if ($previous = $this->previousCursor()) {
            return $this->url([
                'before' => $previous,
            ]);
        }

        return null;
    }

    /**
     * @param array $cursor
     *
     * @return string
     */
    public function url($cursor = [])
    {
        return $this->path
            .(Str::contains($this->path, '?') ? '&' : '?')
            .Arr::query(array_merge($this->query, $cursor))
            .$this->buildFragment();
    }

    /**
     * The URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->nextCursor()) {
            $query = [
                'after' => $this->nextCursor(),
            ];

            return $this->url($query);
        }

        return null;
    }
}
