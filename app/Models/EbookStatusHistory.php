<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbookStatusHistory extends Model
{
  protected $table = 'ebook_status_history';

  public $timestamps = false;

  protected $fillable = [
    'ebook_id',
    'old_status',
    'new_status',
    'changed_at',
  ];

  protected function casts(): array
  {
    return [
      'old_status' => 'boolean',
      'new_status' => 'boolean',
      'changed_at' => 'datetime',
    ];
  }

  public function ebook(): BelongsTo
  {
    return $this->belongsTo(Ebook::class);
  }
}
