<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    $rules = [
      'email' => ['required', 'email', 'max:191'],
      'channel' => ['required', 'string', Rule::in(UserChannelEnum::getValues())],
      'provider' => ['required', 'string', Rule::in(UserProviderEnum::getValues())]
    ];

    if ($this->input('provider') == UserProviderEnum::Local) {
      $rules['password'] = ['required', 'string'];
    }

    if ($this->input('provider') == UserProviderEnum::Google) {
      $rules['google_access_token'] = ['required', 'string'];
    }

    return $rules;
  }
}