<?php

namespace App\Actions\Tenant\Events;

use App\Http\Resources\EventResource;
use App\Services\FileService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class Index
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    $tenant = tenant();
    $currentPage = $request->get('page', 1);
    $perPage = $request->get('limit', 10);
    $search = $request->get('search', '');

    try {
      $events = $tenant->events()
        ->withMedia(['image'])
        ->when(strlen($search) > 0, function ($query) use ($search) {
          $query->where('title', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $currentPage);

      $events->getCollection()->transform(function ($event) {
        $image = FileService::getFileUrlFromMedia($event->media->first());

        return EventResource::make($event, $image);
      });

      return $this->paginationResponse($events, 'Successfully retrieved events.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}