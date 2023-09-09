<?php

namespace App\Actions\Tenant\Events;

use App\Models\Event;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Delete
{
  use AsAction, ApiResponse;

  public function handle(Event $event)
  {
    try {
      $event->delete();

      return $this->successResponse('Event deleted successfully.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
