<?php

namespace App\Http\Middleware;

use App\Actions\Admin\OrganizationApproval;
use App\Enums\OrganizationApprovalEnum;
use App\Enums\OrganizationStatus;
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
      return $this->notFoundResponse(null, 'Organization not found.');
    }

    // Check tenant status
    if ($tenant->status == OrganizationStatus::Disabled) {
      return $this->badRequestResponse(null, 'Organization is disabled. Please contact support for more information.');
    }

    if ($tenant->approval == OrganizationApprovalEnum::Pending) {
      return $this->badRequestResponse(null, 'Organization is pending approval. Please contact support for more information.');
    }

    if ($tenant->approval == OrganizationApprovalEnum::Rejected) {
      return $this->badRequestResponse(null, 'Organization\'s approval has been rejected. Please contact support for more information.');
    }

    // Add tenant to request
    $request->merge(['xTenant' => $tenant]);

    //

    return $next($request);
  }
}
