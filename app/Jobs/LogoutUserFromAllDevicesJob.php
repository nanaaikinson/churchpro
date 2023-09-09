<?php

namespace App\Jobs;

use App\Helpers\Broadcast;
use App\Helpers\Constants;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogoutUserFromAllDevicesJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(private readonly User $user)
  {
    //
  }

  public function handle(): void
  {
    try {
      // Invalidate all auth tokens
      $this->user->authTokens()->update(['whitelisted' => false]);

      // TODO: Mobile push notification
      $this->user->devices()->where('active', true)->get()
        ->each(function () {
          //
        });

      // Web push notification
      Broadcast::trigger(Constants::USER_CHANNEL . $this->user->id, 'LOGOUT', [
        'action' => 'LOGOUT',
        'message' => 'You have been logged out from all devices.'
      ]);
    } catch (\Exception $e) {
      Log::error('An error occurred while logging out user from all devices' . $e->getMessage());
    }
  }
}
