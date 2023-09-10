<?php

namespace App\Actions\Tenant\Events;

use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Update
{
  use AsAction, ApiResponse;

  public function handle(StoreEventRequest $request, Event $event)
  {
    try {
      $event->update([
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'location' => $request->input('location'),
        'published' => $request->input('published'),
      ]);

      if ($request->input('image')) {
        $event->syncMedia($request->input('image'), 'excerpt');
      }

      $data = EventResource::make($event);

      return $this->dataResponse($data, 'Event updated successfully.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
