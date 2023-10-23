<?php

namespace App\Actions\Tenant\Organization;

use App\Http\Requests\StoreOrganizationRequest;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use DB;

class Update
{
  use AsAction, ApiResponse;

  public function handle(StoreOrganizationRequest $request)
  {
    DB::beginTransaction();

    try {
      $tenant = tenant();
      $logo = $request->input('logo');
      $socials = $request->input('socials');

      $data = json_decode($tenant->data, true);

      $tenant->update([
        'name' => $request->input('name'),
        'data' => json_encode([
          'about' => $request->input('about'),
          'email' => $request->input('email'),
          'phone_number' => $request->input('phone_number'),
          'location' => $request->input('location'),
          'socials' => $socials,
          'representative' =>  $data['representative'] ? ['role' => $data['representative']['role']] : null,
        ]),
      ]);

      if ($logo) {
        $tenant->syncMedia($logo, 'logo');
      }

      DB::commit();

      return $this->messageResponse('Organization updated successfully');
    } catch (\Exception $e) {
      DB::rollBack();

      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
