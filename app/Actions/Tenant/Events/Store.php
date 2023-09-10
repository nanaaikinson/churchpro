<?php

namespace App\Actions\Tenant\Events;

use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class Store
{
  use AsAction, ApiResponse;

  public function handle(StoreEventRequest $request)
  {
    DB::beginTransaction();

    try {
      $event = Event::create([
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'location' => $request->input('location'),
        'published' => $request->input('published'),
      ]);

      if ($request->input('image')) {
        $event->attachMedia($request->input('image'), 'excerpt');
      }

      DB::commit();

      $data = EventResource::make($event);

      return $this->dataResponse($data, 'Event created successfully.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
