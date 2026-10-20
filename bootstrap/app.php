<?php

use App\Exceptions\ApiErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;


return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    //
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(
      fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
    );

    $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
      if ($request->routeIs('login.store') && ! $request->expectsJson()) {
        return back()
          ->withInput($request->only('email'))
          ->withErrors(['credentials' => 'Terlalu banyak percobaan. Coba lagi sebentar lagi.']);
      }
    });

    $exceptions->render(
      fn(Throwable $exception, Request $request) => ApiErrorResponse::from($exception, $request),
    );
  })->create();
