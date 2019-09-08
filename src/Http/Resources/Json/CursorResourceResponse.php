<?php

namespace TehekOne\CursorPagination\Http\Resources\Json;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;

/**
 * Class CursorResourceResponse
 *
 * @package TehekOne\CursorPagination\Http\Resources\Json
 */
class CursorResourceResponse extends PaginatedResourceResponse
{
    /**
     * Add the pagination information to the response.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->resource->toArray();

        return [
            'paging' => $paginated['paging'],
        ];
    }
}
