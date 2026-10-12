<?php

namespace App\Exceptions;

use Exception;

class DuplicateEbookException extends Exception
{
    public function __construct(public readonly int $existingId)
    {
        parent::__construct('PDF ini sudah ada di koleksi Anda.');
    }
}
