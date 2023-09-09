<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrganizationApprovalUpdatedEvent implements ShouldBroadcast
{
  public function __construct(private readonly User $user)
  {
    //
  }

  public function broadcastOn(): Channel
  {
    return new PrivateChannel('Auth.User.' . $this->user->id);
  }
}