<?php

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimiterTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    // Bersihkan hit cache rate limiter sebelum setiap test
    RateLimiter::clear('login');
  }

  public function test_login_rate_limiter_block_after_5_attempts(): void
  {
    $loginData = [
      'email' => 'user1@example.test',
      'password' => 'qwertyuio',
    ];

    // 1. Eksekusi 5 percobaan pertama (Harus lolos / tidak Kena Rate Limit)
    for ($i = 0; $i < 5; $i++) {
      $response = $this->postJson('/login', $loginData);

      // Pastikan status bukan 429 pada 5 percobaan awal
      $response->assertStatus(302); // atau status validasi gagal lainn
    }

    // 2. Percobaan ke-6 harus memicu HTTP 429 (Too Many Requests)
    $response = $this->postJson('/login', $loginData);

    $response->assertStatus(429);
  }
}
