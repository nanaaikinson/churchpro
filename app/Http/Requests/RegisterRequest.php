<?php

namespace App\Http\Requests;

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
    $provider = $this->input('provider');
    $password = Password::min(8)->mixedCase()->letters()->numbers();

    // Validation rules
    $rules = [
      'first_name' => ['required', 'string', 'max:191'],
      'last_name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'string', 'email', 'max:191'],
      'channel' => ['required', 'string', 'max:191', Rule::in(UserChannelEnum::Mobile, UserChannelEnum::Tenant)],
      'provider' => ['required', 'string', 'max:191', new EnumValue(UserProviderEnum::class)],
    ];

    // Provide password if local provider
    if ($provider == UserProviderEnum::Local) {
      $rules['password'] = ['required', 'max:100', 'confirmed', in_production() ? $password->uncompromised() : $password];
    }

    return $rules;
  }
}