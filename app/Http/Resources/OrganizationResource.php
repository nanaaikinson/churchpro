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
      'status' => $this->status,
      'approval' => $this->approval,
      'logo' => $this->logo,
      'created_at' => $this->created_at,
      'data' => json_decode($this->data)
    ];
  }
}
