<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsBoolean implements ValidationRule
{
  /**
   * Run the validation rule.
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    $result = is_bool(to_boolean($value));

    if (!$result) {
      $fail('The :attribute must be a boolean.');
    }
  }
}
