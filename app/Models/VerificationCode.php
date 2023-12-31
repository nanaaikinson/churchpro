<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @mixin IdeHelperVerificationCode
 */
class VerificationCode extends Model
{
  use HasFactory, HasUlids;

  protected $casts = [
    'expires_at' => 'datetime'
  ];

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Morph verifiable
  public function verifiable()
  {
    return $this->morphTo();
  }
}