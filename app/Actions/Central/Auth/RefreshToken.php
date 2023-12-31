<?php

namespace App\Actions\Central\Auth;

use App\Helpers\AuthHelper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class RefreshToken
{
  use AsAction, ApiResponse;

  public function handle(Request $request)
  {
    try {
      $payload = auth('api')->payload();
      $tokenType = $payload->get('type');

      if (!$tokenType || $tokenType !== 'refresh') {
        throw new \Exception('Invalid token provided.');
      }

      $token = AuthHelper::refreshToken();

      return $this->dataResponse($token, 'Token refreshed successfully.');
    } catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
