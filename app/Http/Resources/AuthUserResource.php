<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
  public function __construct($resource, private readonly bool $relations = false)
  {
    parent::__construct($resource);
  }

  public function toArray($request)
  {
    $media = $this->whenLoaded('media') ? $this->media->where('tag', 'avatar')->first() : null;

    $data = [
      'id' => $this->id,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'email' => $this->email,
      'avatar' => FileService::getFileUrlFromMedia($media),
      'email_verified' => $this->email_verified_at ? true : false,
    ];

    if ($this->relations) {
      $organizations = $this->whenLoaded('organizations') ? $this->organizations : $this->organizations();
      $data['organizations'] = OrganizationResource::collection($organizations);
    }

    return $data;
  }
}
