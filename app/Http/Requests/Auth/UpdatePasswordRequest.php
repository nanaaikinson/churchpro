<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    $rule = Password::min(8)->letters()->numbers()->mixedCase()->symbols();
    $user = auth('api')->user();

    return [
      'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
        if (!\Hash::check($value, $user->password)) {
          return $fail('The current password is incorrect.');
        }

        if ($value == $this->input('password')) {
          return $fail('The new password must be different from the current password.');
        }
      }],
      'new_password' => ['required', 'string', 'max:50', in_production() ? $rule->uncompromised() : $rule],
      'confirm_new_password' => ['required', 'string', 'same:password']
    ];
  }
}
