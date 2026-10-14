<?php

namespace App\Exceptions;

use RuntimeException;

class EbookFileMissingException extends RuntimeException
{
  public function __construct(public readonly int $ebookId)
  {
    parent::__construct('File ebook tidak tersedia di storage.');
  }
}
