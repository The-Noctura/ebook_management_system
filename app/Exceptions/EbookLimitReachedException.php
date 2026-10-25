<?php

namespace App\Exceptions;

use RuntimeException;

class EbookLimitReachedException extends RuntimeException
{
  public function __construct()
  {
    parent::__construct('Koleksi sudah penuh (maksimal 10 PDF). Hapus salah satu untuk menambah.');
  }
}
