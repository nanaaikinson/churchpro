<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
  public function __construct($resource, private readonly string|null $image)
  {
    parent::__construct($resource);
  }

  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'description' => $this->description,
      'start_date' => $this->start_date,
      'end_date' => $this->end_date,
      'location' => $this->location,
      'published' => $this->published,
      'created_at' => $this->created_at,
      'image' => $this->image,
    ];
  }
}