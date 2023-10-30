<?php

namespace App\Http;

use App\Enums\CodeEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;

class Response
{
    public function success(mixed $data = null, string $message = 'ok'): JsonResponse
    {
        return $this->send($data, $message, CodeEnum::SUCCESS);
    }

    public function fail(string $message = 'fail', CodeEnum $codeEnum = null, int $status = 200): JsonResponse
    {
        return $this->send(null, $message, $codeEnum ?: CodeEnum::FAIL, $status);
    }

    public function send(mixed $data, string $message, CodeEnum $codeEnum, int $status = 200)
    {
        return new JsonResponse(['code' => $codeEnum->value, 'message' => $message, 'data' => $this->formatData($data)], $status);
    }

    protected function formatData($data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator || $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable || (is_object($data) && method_exists($data, 'toArray')) => $data->toArray(),
            default => $data
        };
    }

    public function jsonResource(JsonResource $resource): array
    {
        return value($this->formatJsonResource(), $resource);
    }

    protected function formatJsonResource(): \Closure
    {
        return function (JsonResource $resource) {
            return array_merge_recursive($resource->resolve(request()), $resource->with(request()), $resource->additional);
        };
    }

    public function paginator(AbstractPaginator|AbstractCursorPaginator|Paginator $resource): array
    {
        return [
            'items' => $resource->toArray()['data'],
            'pagination' => $this->formatMeta($resource),
        ];
    }


    public function resourceCollection(ResourceCollection $collection): array
    {
        return [
            'items' => $collection->resolve(),
            'pagination' => $this->formatMeta($collection->resource),
        ];
    }

    /**
     * Format paginator data.
     */
    protected function formatMeta($collection): array
    {
        return match (true) {
            $collection instanceof CursorPaginator => [
                'current' => $collection->cursor()?->encode(),
                'prev' => $collection->previousCursor()?->encode(),
                'next' => $collection->nextCursor()?->encode(),
                'count' => count($collection->items()),
            ],
            $collection instanceof LengthAwarePaginator => [
                'count' => $collection->lastItem(),
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'total' => $collection->total(),
            ],
            $collection instanceof Paginator => [
                'count' => $collection->lastItem(),
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
            ],
            default => [],
        };
    }
}
