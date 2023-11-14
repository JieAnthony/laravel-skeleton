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
    /**
     * @param $data
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    public function success($data = null, string $message = 'ok', array $headers = []): JsonResponse
    {
        return $this->send($data, $message, CodeEnum::SUCCESS, headers: $headers);
    }

    /**
     * @param string $message
     * @param int|CodeEnum|null $code
     * @param int $status
     * @return JsonResponse
     */
    public function fail(string $message = 'fail', int|CodeEnum $code = null, int $status = 200): JsonResponse
    {
        return $this->send(null, $message, $code ?: CodeEnum::FAIL, $status);
    }

    /**
     * @param mixed $data
     * @param string $message
     * @param int|CodeEnum $code
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function send(mixed $data, string $message, int|CodeEnum $code, int $status = 200, array $headers = [])
    {
        return new JsonResponse(
            [
                'code' => $this->formatCode($code),
                'message' => $message,
                'data' => $this->formatData($data)
            ], $status, $headers
        );
    }

    /**
     * @param int|CodeEnum $code
     * @return int
     */
    protected function formatCode(int|CodeEnum $code)
    {
        return $code instanceof \BackedEnum ? $code->value : $code;
    }

    /**
     * @param $data
     * @return array|Arrayable|AbstractPaginator|mixed|mixed[]|null
     */
    protected function formatData($data)
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator || $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable || (is_object($data) && method_exists($data, 'toArray')) => $data->toArray(),
            default => $data
        };
    }

    /**
     * @param JsonResource $resource
     * @return mixed
     */
    public function jsonResource(JsonResource $resource)
    {
        return value($this->formatJsonResource(), $resource);
    }

    /**
     * @return \Closure
     */
    protected function formatJsonResource()
    {
        return function (JsonResource $resource) {
            return array_merge_recursive($resource->resolve(request()), $resource->with(request()), $resource->additional);
        };
    }

    /**
     * @param AbstractPaginator|AbstractCursorPaginator|Paginator $resource
     * @return array
     */
    public function paginator(AbstractPaginator|AbstractCursorPaginator|Paginator $resource)
    {
        return [
            'items' => $resource->toArray()['data'],
            'pagination' => $this->formatMeta($resource),
        ];
    }

    /**
     * @param ResourceCollection $collection
     * @return array
     */
    public function resourceCollection(ResourceCollection $collection)
    {
        return [
            'items' => $collection->resolve(),
            'pagination' => $this->formatMeta($collection->resource),
        ];
    }

    /**
     * @param $collection
     * @return array
     */
    protected function formatMeta($collection)
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
