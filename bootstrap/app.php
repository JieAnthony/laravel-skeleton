<?php

use App\Exceptions\BusinessException;
use App\Http\Middleware\Authenticate;
use App\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/admin.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            Route::redirect('/', '/api');
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 全局中间件
        $middleware->use([
            // \Illuminate\Http\Middleware\TrustHosts::class,
            // \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            // \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            // \Illuminate\Http\Middleware\ValidatePostSize::class,
            // \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            // \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 不报告的异常
        $exceptions->dontReport([
            BusinessException::class,
        ]);

        // HTTP异常
        $exceptions->render(function (HttpException $exception) {
            // 模型不存在异常
            if ($exception->getPrevious() instanceof ModelNotFoundException) {
                return app(Response::class)->error('Access Denied!', 403);
            }

            return app(Response::class)->error($exception->getMessage(), $exception->getStatusCode());
        });

        // 验证异常
        $exceptions->render(function (ValidationException $exception) {
            return app(Response::class)->fail($exception->validator->errors()->first());
        });

        // 认证异常
        $exceptions->render(function (AuthenticationException $exception) {
            return app(Response::class)->error($exception->getMessage(), 401);
        });

        // 未知的异常统一渲染
        $exceptions->render(function (\Throwable $exception) {

            $response = app(Response::class);

            if (App::hasDebugModeEnabled()) {
                $data = [
                    'exception' => $exception::class,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    // 'trace' => collect($exception->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
                ];

                return $response->error($exception->getMessage(), data: $data);
            }

            return $response->error('Server Error');
        });

    })->create();
