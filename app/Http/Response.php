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
    public function success(mixed $data = null, string $message = 'ok', array $headers = []): JsonResponse
    {
        return $this->send($data, $message, CodeEnum::SUCCESS, headers: $headers);
    }

    public function fail(string $message = 'fail', int|CodeEnum|null $code = null, int $status = 200): JsonResponse
    {
        return $this->send(null, $message, $code ?: CodeEnum::FAIL, $status);
    }

    public function send(mixed $data, string $message, int|CodeEnum $code, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(
            [
                'code' => $this->formatCode($code),
                'message' => $message,
                'data' => $this->formatData($data),
            ], $status, $headers
        );
    }

    /**
     * @return int
     */
    protected function formatCode(int|CodeEnum $code)
    {
        return $code instanceof \BackedEnum ? $code->value : $code;
    }

    /**
     * @return array|Arrayable|AbstractPaginator|mixed|mixed[]|null
     */
    protected function formatData($data)
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator || $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable || (\is_object($data) && \method_exists($data, 'toArray')) => $data->toArray(),
            default => $data
        };
    }

    /**
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
            return \array_merge_recursive($resource->resolve(), $resource->additional);
        };
    }

    /**
     * @return array
     */
    public function paginator(AbstractPaginator|AbstractCursorPaginator|Paginator $resource)
    {
        return [
            'items' => $resource->items(),
            'pagination' => $this->formatMeta($resource),
        ];
    }

    /**
     * @return array
     */
    public function resourceCollection(ResourceCollection $collection)
    {
        $result = [
            'items' => $collection->resolve(),
            'pagination' => $this->formatMeta($collection->resource),
        ];

        if ($collection->additional) {
            $result['additional'] = $collection->additional;
        }

        return $result;
    }

    /**
     * @return array|null
     */
    protected function formatMeta($collection)
    {
        return match (true) {
            $collection instanceof CursorPaginator => [
                'current' => $collection->cursor()->encode(),
                'prev' => $collection->previousCursor()->encode(),
                'next' => $collection->nextCursor()->encode(),
                'count' => \count($collection->items()),
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
            default => null,
        };
    }
}
