<?php

namespace App\Actions\Admin;

use App\Enums\OrganizationApproval as OrganizationApprovalEnum;
use App\Enums\UserOnboardingStepEnum;
use App\Helpers\Broadcast;
use App\Helpers\CamelCaseConverter;
use App\Mail\OrganizationApprovalMail;
use App\Models\Organization;
use App\Models\User;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use DB;

class OrganizationApproval
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return [
      'organizations' => ['required', 'array'],
      'organizations.*' => ['required', 'string', Rule::exists('organizations', 'id')],
      'approval' => ['required', 'string', new EnumValue(\App\Enums\OrganizationApproval::class)]
    ];
  }

  public function handle(ActionRequest $request)
  {
    DB::beginTransaction();

    try {
      // Change approval status
      $organizations = $request->input('organizations');
      $approval = $request->input('approval');
      Organization::whereIn('id', $organizations)->update(['approval' => $approval]);

      // Get users
      $results = DB::table('users')
        ->select('users.id', 'users.email', 'users.first_name', 'users.last_name', 'organizations.name as organization_name', 'organizations.id as organization_id')
        ->leftJoin('iams', 'users.id', '=', 'iams.user_id')
        ->leftJoin('organizations', 'organizations.id', '=', 'iams.organization_id')
        ->where('iams.root', true)
        ->whereIn('organizations.id', $request->input('organizations'))
        ->get();

      // Send emails
      $results->each(function ($result) use ($approval) {
        $data = [
          'name' => "{$result->first_name} {$result->last_name}",
          'organization_name' => $result->organization_name,
          'approval' => $approval,
          'onboarding_step' => $approval == OrganizationApprovalEnum::Approved ? UserOnboardingStepEnum::Complete : UserOnboardingStepEnum::TenantRejection,
        ];

        dispatch(function () use ($result, $data) {
          // Mail::to($result->email)->send(new OrganizationApprovalMail($data));
          User::where('id', $result->id)->update(['onboarding_step' => $data['onboarding_step']]);

          Broadcast::trigger(
            channel: "Auth.User." . $result->id,
            event: "ORGANIZATION_APPROVAL",
            data: json_encode(CamelCaseConverter::run($data))
          );
        })->afterCommit();
      });

      DB::commit();

      return $this->messageResponse('Successfully updated ' . Str::plural('organization', count($organizations)) . ' approval ' . Str::plural('status', count($organizations)) . ' .');

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}