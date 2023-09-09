<?php

namespace App\Http\Requests;

use App\Rules\IsBoolean;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreEventRequest extends FormRequest
{
  /**
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:191'],
      'description' => ['required', 'string'],
      'start_date' => ['required', 'date'],
      'end_date' => ['required', 'date'],
      'location' => ['nullable', 'string'],
      'published' => ['required', new IsBoolean],
      'image' => ['nullable', 'string', Rule::exists('media', 'id')],
    ];
  }
}
