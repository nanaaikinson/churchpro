<?php

namespace App\Http\Requests;

use App\Enums\DeviceTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'device_id' => ['required', 'string', 'max:191'],
      'fcm_token' => ['required', 'string', 'max:191'],
      'type' => ['required', Rule::in([DeviceTypeEnum::Ios, DeviceTypeEnum::Android])]
    ];
  }
}
