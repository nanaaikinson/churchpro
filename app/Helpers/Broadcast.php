<?php

namespace App\Helpers;

use Pusher\Pusher;

class Broadcast
{
  public static function trigger(string $channel, string $event, mixed $data)
  {
    $pusher = new Pusher(
      config('broadcasting.connections.pusher.key'),
      config('broadcasting.connections.pusher.secret'),
      config('broadcasting.connections.pusher.app_id'),
      [
        'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        'useTLS' => true,
      ]
    );

    $pusher->trigger($channel, $event, $data);
  }
}