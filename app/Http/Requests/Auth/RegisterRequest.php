<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    $channel = $this->input('channel');
    $provider = $this->input('provider');
    $password = Password::min(8)->letters()->mixedCase()->numbers();

    $rules = [
      'first_name' => ['required', 'string', 'max:191'],
      'last_name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
    ];

    // Mobile validation
    if ($channel == UserChannelEnum::Mobile) {
      $rules['provider'] = ['required', 'string', new EnumValue(UserProviderEnum::class)];

      if ($provider == UserProviderEnum::Local) {
        $rules['password'] = ['required', 'max:191', in_production() ? $password->uncompromised() : $password];
      }
    }

    return $rules;
  }

  public function messages()
  {
    return [
      'email.unique' => ''
    ];
  }
}
