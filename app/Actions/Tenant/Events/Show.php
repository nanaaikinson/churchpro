<?php

namespace App\Actions\Tenant\Events;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Show
{
  use AsAction, ApiResponse;

  public function handle(Event $event)
  {
    try {
      return $this->dataResponse(EventResource::make($event), 'Successfully retrieved event.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
