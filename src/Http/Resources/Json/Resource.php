<?php

namespace TehekOne\LaravelCursorPagination\Http\Resources\Json;

use Illuminate\Http\Resources\Json\Resource as BaseResource;

/**
 * Class Resource
 *
 * @package TehekOne\LaravelCursorPagination\Http\Resources\Json
 */
class Resource extends BaseResource
{
    /**
     * Create new cursor resource collection.
     *
     * @param mixed $resource
     *
     * @return CursorResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new CursorResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
