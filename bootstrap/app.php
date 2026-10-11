<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $exception, Request $request): ?JsonResponse {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Sesi tidak valid. Silakan login kembali.',
                'code' => 'unauthenticated',
            ], 401);
        });

        $exceptions->render(function (Throwable $exception, Request $request): ?JsonResponse {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($exception instanceof ValidationException || $exception instanceof AuthenticationException || $exception instanceof HttpResponseException) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $code = match (true) {
                $status === 401 => 'unauthenticated',
                $status === 404 => 'not_found',
                $status === 429 => 'too_many_attempts',
                $status >= 500 => 'server_error',
                default => 'http_error',
            };
            $message = match ($code) {
                'unauthenticated' => 'Sesi tidak valid. Silakan login kembali.',
                'not_found' => 'Endpoint tidak ditemukan.',
                'too_many_attempts' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                'server_error' => 'Terjadi kesalahan pada server.',
                default => 'Permintaan tidak dapat diproses.',
            };
            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            return response()->json([
                'message' => $message,
                'code' => $code,
            ], $status, $headers);
        });
    })->create();
