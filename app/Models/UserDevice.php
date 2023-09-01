<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperUserDevice
 */
class UserDevice extends Model
{
  use HasFactory, HasUlids;

  public function newUniqueId(): string
  {
    return ((string) Str::ulid());
  }

  // Relationship
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
