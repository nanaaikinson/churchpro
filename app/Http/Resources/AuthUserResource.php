<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
  public function toArray($request)
  {
    $media = $this->whenLoaded('media') ? $this->media->where('tag', 'avatar')->first() : null;

    $organizations = $this->whenLoaded('organizations') ? $this->organizations : $this->organizations();

    return [
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'email' => $this->email,
      'avatar' => FileService::getFileUrlFromMedia($media),
      'onboarding_step' => $this->onboarding_step,
      'organizations' => OrganizationResource::collection($organizations),
    ];
  }
}
