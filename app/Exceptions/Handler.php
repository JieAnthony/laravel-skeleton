<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        /**
         * 响应 表单验证错误
         */
        $this->renderable(function (ValidationException $e) {
            return $this->response()->fail($e->validator->errors()->first(), 422);
        });

        /**
         * 上错 未知错误
         */
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function renderExceptionResponse($request, Throwable $e)
    {
        $code = $this->isHttpException($e) ? $e->getStatusCode() : 500;

        return $this->response()->send(
            $this->convertExceptionToArray($e),
            $e->getMessage(),
            $code,
            $code
        );
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        return config('app.debug') ? [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : null;
    }
}
