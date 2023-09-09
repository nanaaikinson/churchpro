<?php

namespace App\Actions\Central\Organization;

use App\Enums\OrganizationApprovalEnum;
use App\Enums\OrganizationStatus;
use App\Enums\UserOnboardingStepEnum;
use App\Http\Requests\StoreOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use App\Traits\ApiResponse;
use DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Str;

class Onboard
{
  use AsAction, ApiResponse;

  public function handle(StoreOrganizationRequest $request): \Illuminate\Http\JsonResponse
  {
    DB::beginTransaction();

    // TODO: Restrict to only one root organization per user

    try {
      /**
       * @var User $user
       */
      $user = $request->user('api');
      $logo = $request->input('logo');

      // Create organization
      $organization = Organization::create([
        'name' => $request->input('name'),
        'approval' => OrganizationApprovalEnum::Pending,
        'status' => OrganizationStatus::Enabled,
        'data' => json_encode([
          'phone_number' => $request->input('phone_number'),
          'location' => $request->input('location'),
          'email' => $request->input('email'),
          'representative' => ['role' => $request->input('role_at_church')]
        ])
      ]);

      // Create default branch
      $branch = $organization->branches()->create([
        'name' => $request->input('default_branch'),
        'slug' => Str::slug("{$organization->id}-" . $request->input('default_branch')),
      ]);

      // Assign user to branch and organization
      $user->organizations()->attach($organization->id);
      $user->branches()->attach($branch->id);
      $user->iams()->create([
        'organization_id' => $organization->id,
        'value' => generate_iam(12, 12),
        'root' => true
      ]);

      // Update user onboarding step
      $user->update(['onboarding_step' => UserOnboardingStepEnum::TenantApproval]);

      // Attach logo if any
      if ($logo) {
        $organization->attachMedia($logo, 'logo');
      }

      // Persist to db
      DB::commit();

      return $this->dataResponse(['onboarding_step' => UserOnboardingStepEnum::TenantApproval], 'Your organization has been on-boarded successfully. Please wait for approval.');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
