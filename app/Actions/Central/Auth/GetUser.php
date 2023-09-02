<?php

namespace App\Actions\Central\Auth;

use App\Rules\IsBoolean;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUser
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
    return ['with_relations' => ['nullable', new IsBoolean]];
  }

  public function handle(ActionRequest $request)
  {
    try {
      /**
       * @var \App\Models\User $user
       */
      $user = $request->user('api');
      $relations = $user->email_verified_at && $request->input('with_relations');

      return $this->dataResponse(Helper::user($user, $relations), 'Successfully retrieved user.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
