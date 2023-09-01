<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class TenantResolverMiddleware
{
  use ApiResponse;

  public function handle(Request $request, Closure $next)
  {
    /**
     * @var \App\Models\User $user
     */
    $user = $request->user('api')->load('organizations', 'organizations.branches', 'organizations.branches.users');
    $tenant = $user->organizations->where('id', $request->header('X-Tenant'))->first();

    // Check if user has access to an organization
    if (!$tenant) {
      return $this->notFoundResponse(null, 'Tenant not found.');
    }

    // Add tenant to request
    $request->merge(['xTenant' => $tenant]);

    //

    return $next($request);
  }
}