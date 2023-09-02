<?php

namespace App\Actions\Central\Organization;

use App\Enums\FileUploadContentTypeEnum;
use App\Enums\OrganizationApproval;
use App\Enums\OrganizationStatus;
use App\Enums\RoleAtChurch;
use App\Enums\UserOnboardingStepEnum;
use App\Models\Organization;
use App\Services\FileService;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Str;

class Onboard
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:191'],
      'role_at_church' => ['required', 'string', new EnumValue(RoleAtChurch::class)],
      'default_branch' => ['required', 'string', 'max:191'],
      'phone_number' => ['required', 'string', 'max:12'],
      'location' => ['required', 'string', 'max:191'],
      'logo' => ['nullable', 'string', Rule::exists('media', 'id')],
    ];
  }

  public function handle(ActionRequest $request): \Illuminate\Http\JsonResponse
  {
    DB::beginTransaction();

    // TODO: Restrict to only one root organization per user

    try {
      /**
       * @var \App\Models\User $user
       */
      $user = $request->user('api');
      $logo = $request->input('logo');

      // Create organization
      $organization = Organization::create([
        'name' => $request->input('name'),
        'approval' => OrganizationApproval::Pending,
        'status' => OrganizationStatus::Enabled,
        'data' => json_encode([
          'phone_number' => $request->input('phone_number'),
          'location' => $request->input('location'),
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

      return $this->messageResponse('Your organization\'s has been on-boarded successfully. Please wait for approval.');

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
