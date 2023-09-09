<?php

namespace App\Actions\Tenant\Events;

use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class Index
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    $currentPage = $request->get('page', 1);
    $perPage = $request->get('limit', 10);
    $search = $request->get('search', '');

    try {
      $events = Event::withMedia(['image'])
        ->when(strlen($search) > 0, function ($query) use ($search) {
          $query->where('title', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $currentPage);

      $events->getCollection()->transform(function ($event) {
        return EventResource::make($event);
      });

      return $this->paginationResponse($events, 'Successfully retrieved events.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
