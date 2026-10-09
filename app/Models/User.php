<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens;

  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  protected function casts(): array
  {
    return [
      'password' => 'hashed',
    ];
  }

  public function ebooks(): HasMany
  {
    return $this->hasMany(Ebook::class);
  }
}
