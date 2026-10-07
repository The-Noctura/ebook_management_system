<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ebook extends Model
{
  protected $fillable = [
    'user_id',
    'title',
    'file_path',
    'file_hash',
    'is_read',
  ];

  protected function casts(): array
  {
    return [
      'is_read' => 'boolean',
    ];
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function statusHistory(): HasMany
  {
    return $this->hasMany(EbookStatusHistory::class);
  }
}
