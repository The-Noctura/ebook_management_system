<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    RateLimiter::for('api-login', function (Request $request): Limit {
      $email = $request->input('email');
      $normalizedEmail = is_string($email) ? strtolower(trim($email)) : '';

      return Limit::perMinute(5)
        ->by($normalizedEmail . '|' . $request->ip())
        ->response(function (Request $request, array $headers) {
          return response()->json([
            'message' => 'Terlalu banyak percobaan login. Silakan coba lagi nanti.',
            'code' => 'too_many_attempts',
          ], 429, $headers);
        });
    });
  }
}
