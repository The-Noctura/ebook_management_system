<?php

namespace App\Http\Requests\Api;

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
}
