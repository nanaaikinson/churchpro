<?php

namespace App\Http\Resources;

use App\Services\FileService;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
  public function toArray($request)
  {
    $media = $this->firstMedia('excerpt');
    $image = FileService::getFileUrlFromMedia($media);

    return [
      'id' => $this->id,
      'title' => $this->title,
      'description' => $this->description,
      'start_date' => $this->start_date,
      'end_date' => $this->end_date,
      'location' => $this->location,
      'published' => $this->published,
      'created_at' => $this->created_at,
      'image' => $image,
    ];
  }
}
