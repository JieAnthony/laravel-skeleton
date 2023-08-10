<?php

namespace App\Http;

use App\Enums\CodeEnum;
use Illuminate\Http\JsonResponse;

class Response
{
    public function success(string $message = 'ok', mixed $data = null): JsonResponse
    {
        return $this->send(CodeEnum::SUCCESS->value, $message, $data);
    }

    public function fail(string $message, CodeEnum $codeEnum = null, int $status = 200): JsonResponse
    {
        return $this->send($codeEnum ? $codeEnum->value : CodeEnum::FAIL->value, $message, $status);
    }

    public function send(int $code, string $message, mixed $data = null, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(\compact('code', 'message', 'data'), $status, $headers);
    }
}
