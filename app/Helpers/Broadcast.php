<?php

namespace App\Helpers;

use Pusher\Pusher;
use Kutia\Larafirebase\Facades\Larafirebase;

class Broadcast
{
  private static Broadcast $instance;
  private Pusher $pusher;

  public function __construct()
  {
    $this->pusher = new Pusher(
      auth_key: config('broadcasting.connections.pusher.key'),
      secret: config('broadcasting.connections.pusher.secret'),
      app_id: config('broadcasting.connections.pusher.app_id'),
      options: [
        'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        'useTLS' => true,
      ]
    );
  }

  private static function getInstance(): Broadcast
  {
    if (!isset(self::$instance)) {
      self::$instance = new Broadcast();
    }

    return self::$instance;
  }

  /**
   * Trigger a web socket notification to a user(s)
   * @param string $channel
   * @param string $event
   * @param mixed $data
   * @return void
   */
  public static function wsNotification(string $channel, string $event, mixed $data)
  {
    self::getInstance()->pusher->trigger($channel, $event, json_encode(CamelCaseConverter::run($data)));
  }

  /**
   * Trigger a fcm push notification to a user(s)
   * @return void
   */
  public static function pushNotification(array $tokens, string $title, string $body, array $data = [])
  {
  }
}
