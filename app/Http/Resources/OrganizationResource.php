<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
  public function toArray($request)
  {
    $this->load('bookmarks', 'likes');

    return [
      'id' => $this->id,
      'name' => $this->name,
      'bookmarks' => $this->bookmarks->count(),
      'likes' => $this->likes->count(),
      'logo' => $this->logo,
      'data' => json_decode($this->data)
    ];
  }
}