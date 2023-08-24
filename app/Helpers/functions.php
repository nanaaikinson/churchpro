<?php

if (!function_exists('in_production')) {
  function in_production(): bool
  {
    return app()->environment('production');
  }
}

if (!function_exists('generate_password')) {
  function generate_password(int $length = 10): string
  {
    // Generate password with uppercase, lowercase, numbers and symbols
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+=', ceil($length / strlen($x)))), 1, $length);
  }
}

if (!function_exists('generate_verification_code')) {
  function generate_verification_code(int $length = 6): string
  {
    // Generate verification code with numbers
    return substr(str_shuffle(str_repeat($x = '0123456789', ceil($length / strlen($x)))), 1, $length);
  }
}

if (!function_exists('organization')) {
  function organization()
  {

  }
}

if (!function_exists('generate_iam')) {
  function generate_iam(int $min = 8, int $max = 10): string
  {
    // Numbers only for IAM between min and max length, unique based on timestamp
    $iam = substr(str_shuffle(str_repeat($x = '0123456789', ceil($max / strlen($x)))), 1, $max);

    return 'IAM' . $iam;
  }
}

if (!function_exists('to_boolean')) {

  /**
   * Convert to boolean
   *
   * @param $booleable
   * @return boolean
   */
  function to_boolean($booleable)
  {
    return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }
}