<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EbookResource;
use App\Services\EbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EbookController extends Controller
{
  public function __construct(private EbookService $ebooks) {}

  public function index(Request $request)
  {
    $user = $request->user();

    $ebooks = $user->ebooks()
      ->orderByDesc('created_at')
      ->orderByDesc('id')
      ->get();

    $readCount = DB::selectOne(
      'SELECT fn_count_read_ebooks(?) AS read_count',
      [$user->id]
    )->read_count;

    return EbookResource::collection($ebooks)->additional([
      'meta' => [
        'total'      => $ebooks->count(),
        'read_count' => (int) $readCount,
      ],
    ]);
  }

  /**
   * POST /api/ebooks (multipart/form-data).
   *
   * DuplicateEbookException dan kegagalan penyimpanan dipetakan
   * oleh ApiErrorResponse (409 / 500).
   */
  public function store(Request $request)
  {
    // Bila file melebihi upload_max_filesize, PHP menolaknya sebelum Laravel menjalankan
    // aturan `max`; tanpa pemeriksaan ini pesan yang muncul menyesatkan ("file wajib diisi").
    $raw = $request->files->get('file');
    if (
      $raw instanceof UploadedFile
      && in_array($raw->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
    ) {
      throw ValidationException::withMessages([
        'file' => ['Ukuran file maksimal 20 MB.'],
      ]);
    }

    $validated = $request->validate([
      'file'  => ['bail', 'required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:20480'],
      'title' => ['nullable', 'string', 'max:255'],
    ], [
      'file.required'  => 'File PDF wajib diisi.',
      'file.file'      => 'File gagal diunggah. Pastikan ukuran file tidak lebih dari 20 MB.',
      'file.mimes'     => 'File harus berformat PDF.',
      'file.mimetypes' => 'File harus berformat PDF.',
      'file.max'       => 'Ukuran file maksimal 20 MB.',
      'title.string'   => 'Judul harus berupa teks.',
      'title.max'      => 'Judul maksimal 255 karakter.',
    ]);

    $ebook = $this->ebooks->store(
      $request->user(),
      $request->file('file'),
      $validated['title'] ?? null
    );

    return (new EbookResource($ebook))
      ->response()
      ->setStatusCode(201);
  }

  /**
   * GET /api/ebooks/{id}/file
   *
   * ModelNotFoundException -> 404 ebook_not_found,
   * EbookFileMissingException -> 404 file_missing (keduanya di ApiErrorResponse).
   */
  public function file(Request $request, int $id): StreamedResponse
  {
    return $this->ebooks->streamFile($request->user(), $id);
  }

  /**
   * DELETE /api/ebooks/{id}
   *
   * ModelNotFoundException -> 404 ebook_not_found (di ApiErrorResponse).
   */
  public function destroy(Request $request, int $id)
  {
    $this->ebooks->destroy($request->user(), $id);

    return response()->json(['message' => 'Ebook berhasil dihapus.']);
  }

  public function update(Request $request, int $id)
  {
    $validated = $request->validate([
      'is_read' => ['required', 'boolean'],
    ]);

    $ebook = $this->ebooks->setReadStatus(
      $request->user(),
      $id,
      (bool) $validated['is_read']
    );

    return new EbookResource($ebook);
  }
}
