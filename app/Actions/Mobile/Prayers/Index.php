<?php

namespace App\Actions\Mobile\Prayers;

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
      /**
       * @var \App\Models\User $user
       */
      $user = auth('api')->user();

      $prayers = $user->prayers()->with('organization')->paginate(10);

      $prayers->getCollection()->transform(function ($item) {
        return PrayerResource::make($item);
      });

      return $this->paginationResponse($prayers);
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}