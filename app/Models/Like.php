<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @mixin IdeHelperLike
 */
class Like extends Model
{
  use HasFactory, HasUlids;

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function likeable()
  {
    return $this->morphTo();
  }
}