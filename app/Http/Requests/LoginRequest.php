<?php

namespace App\Http\Requests;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    $channel = $this->input('channel');
    $provider = $this->input('provider');

    // Set rules
    $rules = [
      'email' => ['required', 'email'],
      'channel' => ['required', 'string', new EnumValue(UserChannelEnum::class)],
      'provider' => ['required', 'string', 'max:191', new EnumValue(UserProviderEnum::class)],
    ];

    // Set password rule if local provider
    if ($provider == UserProviderEnum::Local) {
      $rules['password'] = ['required', 'string'];
    }

    // Set iam rule if tenant channel
    if ($channel == UserChannelEnum::Tenant) {
      $rules['iam'] = ['required', 'string'];
    }

    return $rules;
  }
}