<?php

namespace App\Actions\Auth;

use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class ResendEmailVerification
{
  use AsAction, ApiResponse;

  public function handle()
  {
    // ...
  }
}
