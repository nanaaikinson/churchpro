<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrayerResource extends JsonResource
{
  public function __construct($resource, private readonly bool $tenant = true)
  {
    parent::__construct($resource);

    $this->withoutWrapping();
  }

  public function toArray($request)
  {
    $data = [
      'id' => $this->id,
      'title' => $this->title,
      'description' => $this->description,
      'created_at' => $this->created_at,
      'status' => null,
    ];

    if ($this->tenant) {
      return array_merge($data, [
        'name' => $this->user->name,
        'email' => $this->user->email,
        'phone_number' => null,
      ]);
    }

    return array_merge($data, [
      'organization' => [
        'id' => $this->organization->id,
        'name' => $this->organization->name,
        'logo' => $this->organization->logo,
      ]
    ]);
  }
}