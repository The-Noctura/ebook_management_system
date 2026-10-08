<?php

namespace Database\Seeders;

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Database\Seeder;

class EbookSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $ebooks = [
      [
        'user_email' => 'alya@example.com',
        'title' => 'Atomic Habits',
        'file_path' => 'ebooks/alya/atomic-habits.pdf',
        'is_read' => true,
      ],
      [
        'user_email' => 'alya@example.com',
        'title' => 'The Pragmatic Programmer',
        'file_path' => 'ebooks/alya/the-pragmatic-programmer.pdf',
        'is_read' => false,
      ],
      [
        'user_email' => 'bima@example.com',
        'title' => 'Clean Code',
        'file_path' => 'ebooks/bima/clean-code.pdf',
        'is_read' => true,
      ],
      [
        'user_email' => 'bima@example.com',
        'title' => 'Design Patterns',
        'file_path' => 'ebooks/bima/design-patterns.pdf',
        'is_read' => false,
      ],
      [
        'user_email' => 'citra@example.com',
        'title' => 'Laskar Pelangi',
        'file_path' => 'ebooks/citra/laskar-pelangi.pdf',
        'is_read' => true,
      ],
      [
        'user_email' => 'citra@example.com',
        'title' => 'Bumi Manusia',
        'file_path' => 'ebooks/citra/bumi-manusia.pdf',
        'is_read' => false,
      ],
      [
        'user_email' => 'dimas@example.com',
        'title' => 'Filosofi Teras',
        'file_path' => 'ebooks/dimas/filosofi-teras.pdf',
        'is_read' => false,
      ],
      [
        'user_email' => 'dimas@example.com',
        'title' => 'Atomic Habits',
        'file_path' => 'ebooks/dimas/atomic-habits.pdf',
        'is_read' => true,
      ],
      [
        'user_email' => 'eka@example.com',
        'title' => 'Sapiens',
        'file_path' => 'ebooks/eka/sapiens.pdf',
        'is_read' => true,
      ],
      [
        'user_email' => 'eka@example.com',
        'title' => 'Educated',
        'file_path' => 'ebooks/eka/educated.pdf',
        'is_read' => false,
      ],
    ];

    foreach ($ebooks as $ebook) {
      $user = User::where('email', $ebook['user_email'])->firstOrFail();

      Ebook::updateOrCreate(
        [
          'user_id' => $user->id,
          'file_hash' => hash('sha256', $ebook['user_email'] . '|' . $ebook['title']),
        ],
        [
          'title' => $ebook['title'],
          'file_path' => $ebook['file_path'],
          'is_read' => $ebook['is_read'],
        ],
      );
    }
  }
}
