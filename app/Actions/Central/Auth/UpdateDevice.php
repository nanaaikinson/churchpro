<?php

namespace App\Actions\Central\Auth;

use App\Http\Requests\UpdateDeviceRequest;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDevice
{
  use AsAction, ApiResponse;

  public function handle(UpdateDeviceRequest $request)
  {
    try {
      /**
       * @var \App\Models\User $user
       */
      $user = auth('api')->user();

      $user->devices()->Create([
        'device_id' => $request->device_id,
        'fcm_token' => $request->fcm_token,
        'type' => $request->type,
        'active' => true
      ]);

      return $this->messageResponse('Device updated successfully');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
