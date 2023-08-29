<?php

namespace App\Actions\Organization;

use App\Enums\FileUploadContentTypeEnum;
use App\Enums\OrganizationApproval;
use App\Enums\OrganizationStatus;
use App\Enums\RoleAtChurch;
use App\Enums\UserOnboardingStepEnum;
use App\Models\Organization;
use App\Services\FileService;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Validation\Rules\File;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use DB;
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
      'logo' => ['nullable', File::types(['jpg', 'jpeg', 'png'])->max(3 * 1024)],
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();

    try {
      /**
       * @var \App\Models\User $user
       */
      $user = $request->user('api');

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

      // Upload logo if any
      if ($request->hasFile('logo')) {
        dispatch(function () use ($request, $organization) {
          $media = FileService::uploadFromSource($request->file('logo'), FileUploadContentTypeEnum::Logo);

          $organization->attachMedia($media, 'logo');
        })->afterCommit();
      }

      // Persist to db
      DB::commit();

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}