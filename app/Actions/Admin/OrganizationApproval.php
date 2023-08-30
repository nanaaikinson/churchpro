<?php

namespace App\Actions\Admin;

use App\Models\Organization;
use App\Models\User;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
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
      Organization::whereIn('id', $request->input('organizations'))
        ->update([
          'approval' => $request->input('approval')
        ]);

      // Get users
      $query = DB::table('users')
        ->join('iams', 'users.id', '=', 'iams.user_id')
        ->join('organizations', 'organizations.id', '=', 'iams.organization_id')
        ->where('iams.root', true)
        ->whereIn('organizations.id', $request->input('organizations'))
        ->get();

      dd($query);

    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}