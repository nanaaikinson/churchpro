<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class CheckAuthTokenValidityMiddleware
{
  use ApiResponse;

  public function handle(Request $request, Closure $next)
  {
    try {
      /**
       * @var JWTGuard $auth
       */
      $auth = auth('api');

      /**
       * @var \App\Models\User $user
       */
      $user = $auth->user();

      $user->load('authTokens');
      $payload = $auth->payload();
      $reference = $payload->get('reference');

      // Check for reference
      if (!$reference) {
        return $this->unauthorizedResponse(null, 'Invalid token.');
      }

      // Check if token is whitelisted
      $token = $user->authTokens->where('reference', $reference)->first();
      if (!$token->whitelisted) {
        return $this->unauthorizedResponse(null, 'Invalid token.');
      }

      return $next($request);
    } catch (\Exception $e) {
      return $this->unauthorizedResponse(null, 'Invalid token.');
    }
  }
}
