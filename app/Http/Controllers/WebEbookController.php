<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateEbookException;
use App\Exceptions\EbookFileMissingException;
use App\Exceptions\EbookLimitReachedException;
use App\Services\EbookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class WebEbookController extends Controller
{
  public function __construct(private readonly EbookService $ebooks) {}

  /**
   * GET /ebooks: daftar koleksi milik user yang sedang login.
   * Koleksi kosong dan koleksi gagal dimuat adalah dua keadaan berbeda
   * (wireframe Empty State bagian 2.2).
   */
  public function index(Request $request): View
  {
    $user = $request->user();

    try {
      $ebooks = $user->ebooks()->orderByDesc('created_at')->orderByDesc('id')->get();

      // Pada koleksi kosong fungsi tidak perlu dipanggil (wireframe Empty State).
      // Mode tanpa objek SQL: hitung dari koleksi yang sudah dimuat (tanpa query tambahan).
      $readCount = match (true) {
        $ebooks->isEmpty() => 0,
        config('ebook.sql_objects') => (int) DB::selectOne('SELECT fn_count_read_ebooks(?) AS total', [$user->id])->total,
        default => $ebooks->where('is_read', true)->count(),
      };

      $loadFailed = false;
    } catch (QueryException $e) {
      report($e);

      $ebooks = collect();
      $readCount = 0;
      $loadFailed = true;
    }

    return view('ebooks.index', compact('ebooks', 'readCount', 'loadFailed'));
  }

  /**
   * GET /ebooks/create: form upload manual (UC-07).
   */
  public function create(): View
  {
    return view('ebooks.create');
  }

  /**
   * POST /ebooks: proses upload (UC-07).
   * Hash, cek duplikat, R2/disk, transaksi, dan compensating action ada di EbookService::store.
   */
  public function store(Request $request): RedirectResponse
  {
    // Upload yang ditolak PHP sebelum validasi Laravel (melebihi upload_max_filesize).
    // Tanpa ini, pesannya salah: "Pilih file PDF terlebih dahulu."
    $upload = $request->file('file');
    $limitLabel = config('ebook.max_upload_label');

    if ($upload !== null && ! $upload->isValid()) {
      $tooBig = in_array($upload->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);

      throw ValidationException::withMessages([
        'file' => $tooBig ? "Ukuran file maksimal {$limitLabel}." : 'File gagal diunggah. Coba lagi.',
      ]);
    }

    $data = $request->validate(
      [
        'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:' . config('ebook.max_upload_kb')],
        'title' => ['nullable', 'string', 'max:255'],
      ],
      [
        'file.required' => 'Pilih file PDF terlebih dahulu.',
        'file.file' => 'File tidak valid. Pilih file PDF.',
        'file.uploaded' => 'File gagal diunggah. Coba lagi.',
        'file.mimes' => 'File harus berformat PDF.',
        'file.mimetypes' => 'File harus berformat PDF.',
        'file.max' => "Ukuran file maksimal {$limitLabel}.",
        'title.string' => 'Judul tidak valid.',
        'title.max' => 'Judul maksimal 255 karakter.',
      ]
    );

    try {
      $ebook = $this->ebooks->store($request->user(), $data['file'], $data['title'] ?? null);
    } catch (DuplicateEbookException $e) {
      return back()
        ->withInput()
        ->withErrors(['file' => 'PDF ini sudah ada di koleksi Anda.'])
        ->with('existing_id', $e->existingId);
    } catch (EbookLimitReachedException $e) {
      return back()
        ->withInput()
        ->withErrors(['file' => $e->getMessage()]);
    } catch (Throwable $e) {
      report($e);

      return back()->withInput()->with('upload_failed', true);
    }

    // ?baru= menyorot kartu baru; fragmen menggulir ke kartu itu.
    return redirect()
      ->to(route('ebooks.index', ['baru' => $ebook->id]) . '#ebook-' . $ebook->id)
      ->with('success', "“{$ebook->title}” ditambahkan ke koleksi.");
  }

  /**
   * GET /ebooks/{id}/file: buka PDF di penampil bawaan browser (UC-08).
   * Ebook milik user lain atau yang tidak ada: 404 (dari ModelNotFoundException).
   */
  public function file(Request $request, int $id): StreamedResponse|RedirectResponse
  {
    try {
      return $this->ebooks->streamFile($request->user(), $id);
    } catch (ModelNotFoundException $e) {
      throw $e;
    } catch (EbookFileMissingException $e) {
      return redirect()->route('ebooks.index')->with('error', 'File PDF tidak tersedia.');
    } catch (Throwable $e) {
      report($e);

      return redirect()->route('ebooks.index')->with('error', 'PDF belum dapat dibuka. Coba lagi.');
    }
  }

  /**
   * PATCH /ebooks/{id}: ubah status baca (UC-09 dan UC-10).
   * Transaksi, procedure, dan idempotensi ada di EbookService::setReadStatus.
   */
  public function update(Request $request, int $id): RedirectResponse
  {
    $data = $request->validate(
      ['is_read' => ['required', 'boolean']],
      [
        'is_read.required' => 'Status baca wajib diisi.',
        'is_read.boolean' => 'Status baca tidak valid.',
      ]
    );

    $back = route('ebooks.index') . '#ebook-' . $id;

    try {
      $ebook = $this->ebooks->setReadStatus($request->user(), $id, (bool) $data['is_read']);
    } catch (ModelNotFoundException $e) {
      throw $e; // ebook tidak ada atau milik user lain: Laravel menjawab 404
    } catch (Throwable $e) {
      report($e);

      return redirect()->to($back)
        ->with('error', 'Status baca belum dapat diubah. Status sebelumnya dipertahankan. Coba lagi.');
    }

    $message = $ebook->is_read
      ? "“{$ebook->title}” ditandai sudah dibaca."
      : "“{$ebook->title}” ditandai belum dibaca.";

    return redirect()->to($back)->with('success', $message);
  }

  /**
   * DELETE /ebooks/{id}: hapus permanen ebook milik user.
   * Urutan hapus (MySQL lalu file) ada di EbookService::destroy.
   * Ebook yang tidak ada atau milik user lain dijawab sama: sudah tidak ada di koleksi.
   */
  public function destroy(Request $request, int $id): RedirectResponse
  {
    try {
      $ebook = $this->ebooks->destroy($request->user(), $id);
    } catch (ModelNotFoundException $e) {
      return redirect()->route('ebooks.index')
        ->with('success', 'Ebook sudah tidak ada di koleksi.');
    } catch (Throwable $e) {
      report($e);

      return redirect()->to(route('ebooks.index') . '#ebook-' . $id)
        ->with('error', 'Ebook belum dapat dihapus. Coba lagi.');
    }

    return redirect()->route('ebooks.index')
      ->with('success', "“{$ebook->title}” dihapus dari koleksi.");
  }
}
