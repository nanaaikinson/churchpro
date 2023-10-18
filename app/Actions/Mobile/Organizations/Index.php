<?php

namespace App\Actions\Mobile\Organizations;

use App\Enums\OrganizationApprovalEnum;
use App\Enums\OrganizationStatus;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class Index
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    try {
      $currentPage = $request->get('page', 1);
      $perPage = $request->get('limit', 10);
      $search = $request->get('search', '');

      $organizations = Organization::query()
        ->where('approval', OrganizationApprovalEnum::Approved)
        ->where('status', OrganizationStatus::Enabled)
        ->when(strlen($search) > 0, function ($query) use ($search) {
          $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name', 'asc')
        ->paginate($perPage, ['*'], 'page', $currentPage);

      $organizations->getCollection()->transform(function ($item) {
        return OrganizationResource::make($item);
      });

      return $this->paginationResponse($organizations, 'Successfully retrieved organizations.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
