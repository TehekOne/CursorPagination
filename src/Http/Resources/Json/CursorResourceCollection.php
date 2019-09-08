<?php

namespace TehekOne\CursorPagination\Http\Resources\Json;

/**
 * Class CursorResourceCollection
 *
 * @package TehekOne\CursorPagination\Http\Resources\Json
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
