<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @mixin IdeHelperPassword
 */
class Password extends Model
{
  use HasFactory, HasUlids;

  protected $hidden = [
    'password',
  ];

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}