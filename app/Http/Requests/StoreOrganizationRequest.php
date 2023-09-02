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
    $rule = Rule::unique('email', 'data->email');
    $email = ($method === 'PUT' || $method === 'PATCH') ? $rule->ignore(tenant()->id) : $rule;

    $rules = [
      'name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'email', 'max:191', $email],
      'phone_number' => ['required', 'string', 'max:12'],
      'location' => ['required', 'string', 'max:191'],
      'logo' => ['nullable', 'string', Rule::exists('media', 'id')],
    ];

    if ($method === 'POST') {
      $rules['default_branch'] = ['required', 'string', 'max:191'];
      $rules['role_at_church'] = ['required', new EnumValue(RoleAtChurch::class)];
    }

    return $rules;
  }
}
