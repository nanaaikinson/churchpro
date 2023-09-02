<?php

namespace App\Actions\Tenant\Organization;

use App\Http\Requests\StoreOrganizationRequest;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Update
{
  use AsAction, ApiResponse;

  public function handle(StoreOrganizationRequest $request)
  {
    // ...
  }
}
