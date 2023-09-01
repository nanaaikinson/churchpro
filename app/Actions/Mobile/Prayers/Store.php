<?php

namespace App\Actions\Mobile\Prayers;

use App\Traits\ApiResponse;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class Store
{
  use AsAction, ApiResponse;

  public function rules()
  {
    return [
      'organization' => ['required', 'string', Rule::exists('organizations', 'id')],
      'title' => ['required', 'string', 'max:191'],
      'description' => ['required', 'string'],
    ];
  }

  public function handle(ActionRequest $request)
  {
    /**
     * @var \App\Models\User $user
     */

    $validated = $request->validated();
    $user = auth('api')->user();

    try {
      $user->prayers()->create([
        'organization_id' => $validated['organization'],
        'title' => $validated['title'],
        'description' => $validated['description'],
      ]);

      // TODO: Send notification to organization admin
      // dispatch(new SendPrayerRequestNotificationJob($validated['organization'], $user));

      return $this->successResponse(['message' => 'Prayer request created successfully']);
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}