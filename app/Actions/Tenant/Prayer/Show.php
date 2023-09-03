<?php

namespace App\Actions\Tenant\Prayer;

use App\Http\Resources\PrayerResource;
use App\Models\Prayer;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Show
{
  use AsAction, ApiResponse;

  public function handle(Prayer $prayer)
  {
    try {
      // Check if prayer belongs to tenant
      $tenant = tenant();

      if ($tenant->id !== $prayer->organization_id) {
        return $this->notFoundResponse(null);
      }

      return $this->dataResponse(PrayerResource::make($prayer, true));

    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}