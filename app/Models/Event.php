<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Str;

class Event extends Model
{
  use HasFactory, HasUlids, Mediable;

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function organization()
  {
    return $this->belongsTo(Organization::class);
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable');
  }
}