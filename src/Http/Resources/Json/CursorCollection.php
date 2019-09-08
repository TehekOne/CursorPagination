<?php

namespace TehekOne\CursorPagination\Http\Resources\Json;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Class CursorResourceCollection
 *
 * @package TehekOne\CursorPagination\Http\Resources\Json
 */
class CursorCollection extends ResourceCollection
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new CursorResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }
}
