<?php

namespace App\Actions\Mobile\Organizations;

use App\Models\Organization;
use App\Traits\ApiResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class Bookmark
{
  use AsAction, ApiResponse;

  public function rules()
  {
    return [
      'organization_id' => 'required|exists:organizations,id'
    ];
  }

  public function handle(ActionRequest $request)
  {
    try {
      $user = auth()->user();
      $organization = Organization::with('bookmarks')->where($request->organization_id)->first();

      if ($organization) {
        $bookmark = $organization->bookmarks->where('user_id', $user->id)->first();

        if ($bookmark) {
          $bookmark->delete();
          return $this->successResponse(null, 'Bookmark removed successfully.');
        }

        $organization->bookmarks()->create([
          'user_id' => $user->id
        ]);

        return $this->successResponse(null, 'Bookmark added successfully.');
      }

      return $this->badRequestResponse(null, 'Organization not found.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
