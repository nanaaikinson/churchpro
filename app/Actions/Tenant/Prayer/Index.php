<?php

namespace App\Actions\Tenant\Prayer;

use App\Http\Resources\PrayerResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class Index
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    try {
      $tenant = tenant();
      $currentPage = $request->get('page', 1);
      $perPage = $request->get('limit', 10);
      $search = $request->get('search', '');

      $prayers = $tenant->prayers()
        ->with('user', 'user.media')
        ->when(strlen($search) > 0, function ($query) use ($search) {
          $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhere('user.first_name', 'like', "%{$search}%")
            ->orWhere('user.last_name', 'like', "%{$search}%")
            ->orWhere('user.email', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $currentPage);

      $prayers->getCollection()->transform(function ($prayer) {
        return PrayerResource::make($prayer, true);
      });

      return $this->paginationResponse($prayers, 'Successfully retrieved prayers.');

    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}