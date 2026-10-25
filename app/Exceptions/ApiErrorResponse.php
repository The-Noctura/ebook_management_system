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

        // 409: PDF duplikat dalam koleksi user (dokumen 04 bagian 5.5).
        if ($exception instanceof DuplicateEbookException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => 'duplicate_ebook',
                'data' => ['existing_id' => $exception->existingId],
            ], 409);
        }

        if ($exception instanceof EbookLimitReachedException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => 'ebook_limit_reached',
            ], 409);
        }

        // 404: baris ebook ada, tetapi file tidak ada di storage (dokumen 04 bagian 5.6).
        if ($exception instanceof EbookFileMissingException) {
            return response()->json([
                'message' => 'File ebook tidak tersedia.',
                'code' => 'file_missing',
            ], 404);
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

        // 413 datang dari ValidatePostSize (body melebihi post_max_size); dokumen 04 bagian 7.1.
        if ($status === 413) {
            $message = 'Ukuran file melebihi batas 20 MB.';
        }

        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

        return response()->json([
            'message' => $message,
            'code' => $code,
        ], $status, $headers);
    }
}
