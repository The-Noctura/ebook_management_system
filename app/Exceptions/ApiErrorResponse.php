<?php

namespace App\Exceptions;

use App\Models\Ebook;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiErrorResponse
{
  public static function from(Throwable $exception, Request $request): ?JsonResponse
  {
    if (! $request->is('api/*')) {
      return null;
    }

    if ($exception instanceof ValidationException || $exception instanceof HttpResponseException) {
      return null;
    }

    $status = match (true) {
      $exception instanceof AuthenticationException => 401,
      $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
      default => 500,
    };

    $previous = $exception->getPrevious();
    $isEbookNotFound = $status === 404
      && $previous instanceof ModelNotFoundException
      && $previous->getModel() === Ebook::class;

    $code = match (true) {
      $status === 401 => 'unauthenticated',
      $isEbookNotFound => 'ebook_not_found',
      $status === 404 => 'not_found',
      $status === 429 => 'too_many_attempts',
      $status >= 500 => 'server_error',
      default => 'http_error',
    };

    $message = match ($code) {
      'unauthenticated' => 'Sesi tidak valid. Silakan login kembali.',
      'ebook_not_found' => 'Ebook tidak ditemukan.',
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
  }
}
