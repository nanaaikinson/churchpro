<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'first_name' => ['required', 'string', 'max:191'],
      'last_name' => ['required', 'string', 'max:191'],
      'username' => ['nullable', 'string', 'max:191', Rule::unique('users', 'username')->ignore($this->user()->id)],
      'avatar' => ['nullable', 'string', 'max:191', Rule::exists('media', 'id')],
    ];
  }
}
