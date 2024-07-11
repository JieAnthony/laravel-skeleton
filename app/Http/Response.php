<?php

namespace App\Http;

use App\Enums\CodeEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    public function error(string $message, int $status = 500, mixed $data = null)
    {
        return $this->send($data, $message, $status, $status);
    }

    public function send(mixed $data, string $message, int|CodeEnum $code, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(
            ['code' => $this->formatCode($code), 'message' => $message, 'data' => $this->formatData($data)],
            $status,
            $headers
        );
    }

    public function noContent()
    {
        return new LaravelResponse(status: 204);
    }

    public function download($file, ?string $name = null, array $headers = [], string $disposition = 'attachment')
    {
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

        if (! \is_null($name)) {
            return $response->setContentDisposition($disposition, $name, \str_replace('%', '', Str::ascii($name)));
        }

        return $response;
    }

    protected function formatCode(int|CodeEnum $code)
    {
        return $code instanceof \BackedEnum ? $code->value : $code;
    }

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

    public function jsonResource(JsonResource $resource)
    {
        return value($this->formatJsonResource(), $resource);
    }

    protected function formatJsonResource()
    {
        return function (JsonResource $resource) {
            return \array_merge_recursive($resource->resolve(), $resource->additional);
        };
    }

    public function paginator(AbstractPaginator|AbstractCursorPaginator|Paginator $resource)
    {
        return [
            'items' => $resource->items(),
            'pagination' => $this->formatMeta($resource),
        ];
    }

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

    protected function formatMeta($collection)
    {
        if ($collection instanceof LengthAwarePaginator) {
            return [
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'total' => $collection->total(),
                // 'count' => $collection->lastItem(),
            ];
        }

        if ($collection instanceof Paginator) {
            return [
                'per_page' => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                // 'count' => $collection->lastItem(),
            ];
        }

        if ($collection instanceof CursorPaginator) {
            return [
                'current' => $collection->cursor()->encode(),
                'prev' => $collection->previousCursor()->encode(),
                'next' => $collection->nextCursor()->encode(),
                // 'count' => \count($collection->items()),
            ];
        }

        return null;
    }
}
