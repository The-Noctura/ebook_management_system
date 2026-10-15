<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EbookResource;
use App\Services\EbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
