<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $users = [
      ['name' => 'Alya Putri', 'email' => 'alya@example.com'],
      ['name' => 'Bima Pratama', 'email' => 'bima@example.com'],
      ['name' => 'Citra Maharani', 'email' => 'citra@example.com'],
      ['name' => 'Dimas Saputra', 'email' => 'dimas@example.com'],
      ['name' => 'Eka Wulandari', 'email' => 'eka@example.com'],
    ];

    foreach ($users as $user) {
      User::updateOrCreate(
        ['email' => $user['email']],
        [
          'name' => $user['name'],
          'password' => 'password',
        ],
      );
    }
  }
}
