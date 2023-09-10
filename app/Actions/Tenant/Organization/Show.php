<?php

namespace App\Actions\Tenant\Organization;

use App\Http\Resources\OrganizationResource;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Show
{
  use AsAction, ApiResponse;

  public function handle()
  {
    try {
      return $this->dataResponse(OrganizationResource::make(tenant()));
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}