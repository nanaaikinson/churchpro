<?php

namespace App\Http\Requests;

use App\Enums\RoleAtChurch;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    $method = $this->method();
    $rule = Rule::unique('organizations', 'data->email');
    $email = (strtoupper($method) === 'PUT' || strtoupper($method) === 'PATCH') ? $rule->ignore(tenant()->id) : $rule;

    $rules = [
      'name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'email', 'max:191', $email],
      'phone_number' => ['required', 'string', 'max:12'],
      'location' => ['required', 'string', 'max:191'],
      'about' => ['nullable', 'string'],
      'logo' => ['nullable', 'string', Rule::exists('media', 'id')],
      'socials.facebook' => ['nullable', 'string', 'max:191'],
      'socials.x' => ['nullable', 'string', 'max:191'],
      'socials.instagram' => ['nullable', 'string', 'max:191'],
      'socials.youtube' => ['nullable', 'string', 'max:191'],
      'socials.tiktok' => ['nullable', 'string', 'max:191'],
      'socials.threads' => ['nullable', 'string', 'max:191'],
      'socials.website' => ['nullable', 'string', 'max:191'],
    ];

    if ($method === 'POST') {
      $rules['default_branch'] = ['required', 'string', 'max:191'];
      $rules['role_at_church'] = ['required', new EnumValue(RoleAtChurch::class)];
    }

    return $rules;
  }
}