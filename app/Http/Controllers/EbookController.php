<?php

namespace App\Http\Controllers;

use App\Models\User;

class EbookController extends Controller
{
  public function index()
  {
    $users = User::all();

    return view('ebooks.index', compact('users'));
  }
}
