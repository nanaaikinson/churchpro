<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @mixin IdeHelperPrayer
 */
class Prayer extends Model
{
  use HasFactory, HasUlids;

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function organization()
  {
    return $this->belongsTo(Organization::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}