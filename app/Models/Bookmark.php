<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Bookmark extends Model
{
  use HasFactory, HasUlids;

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function bookmarkable()
  {
    return $this->morphTo();
  }
}