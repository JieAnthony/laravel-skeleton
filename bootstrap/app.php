<?php

use App\Http\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
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

        $middleware->api(
            remove: [
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ]
        );

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 不报告的异常
        $exceptions->dontReport([
            \App\Exceptions\BusinessException::class,
        ]);

        // 未知的异常统一渲染
        $exceptions->render(function (\Exception $exception) {
            $response = app(Response::class);

            if ($exception instanceof HttpException) {
                return $response->error($exception->getMessage(), $exception->getStatusCode());
            }

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
