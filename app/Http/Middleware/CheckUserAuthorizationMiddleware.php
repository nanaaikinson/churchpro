<?php

namespace App\Http\Middleware;

use App\Enums\UserStatusEnum;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CheckUserAuthorizationMiddleware
{
  use ApiResponse;

  public function handle(Request $request, Closure $next)
  {
    /**
     * @var \App\Models\User $user
     */
    $user = $request->user('api');
    $user->load('organizations');

    // Check if user's account is verified
    if (!$user->email_verified_at) {
      return $this->unauthorizedResponse([
        'action' => 'verify_email',
      ], 'Your account is not verified. Please verify your account.');
    }

    // Check if user's account is active
    if ($user->status == UserStatusEnum::Suspended) {
      return $this->unauthorizedResponse([
        'action' => 'contact_support',
      ], 'Your account is not active. Please contact support.');
    }

    // Check if user has access to an organization
    if ($user->organizations->isEmpty()) {
      return $this->unauthorizedResponse([
        'action' => 'contact_support',
      ], 'You do not have access to any organization. Please contact support.');
    }

    return $next($request);
  }
}