<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    if (is_string($this->input('email'))) {
      $this->merge([
        'email' => strtolower(trim($this->input('email'))),
      ]);
    }
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:100'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'string', 'confirmed', Password::min(8)],
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'Nama wajib diisi.',
      'name.max' => 'Nama maksimal 100 karakter.',
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
      'email.max' => 'Email maksimal 255 karakter.',
      'email.unique' => 'Email sudah digunakan.',
      'password.required' => 'Password wajib diisi.',
      'password.min' => 'Password minimal 8 karakter.',
      'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];
  }
}
