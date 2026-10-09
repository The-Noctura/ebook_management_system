<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
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

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, array<int, string>|string>
   */
  public function rules(): array
  {
    return [
      'email' => ['required', 'string', 'email', 'max:255'],
      'password' => ['required', 'string'],
      'device_name' => ['sometimes', 'string', 'max:255'],
    ];
  }
}
