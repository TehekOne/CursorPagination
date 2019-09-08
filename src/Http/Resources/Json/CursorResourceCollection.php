<?php

namespace TehekOne\LaravelCursorPagination\Http\Resources\Json;

/**
 * Class CursorResourceCollection
 *
 * @package TehekOne\LaravelCursorPagination\Http\Resources\Json
 */
class CursorResourceCollection extends CursorCollection
{
    /**
     * Create a new anonymous resource collection.
     *
     * @param mixed $resource
     * @param string $collects
     *
     * @return void
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }
}
