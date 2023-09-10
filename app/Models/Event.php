<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Str;

/**
 * @mixin IdeHelperEvent
 */
class Event extends Model
{
  use HasFactory, HasUlids, Mediable;

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  protected static function booted(): void
  {
    static::addGlobalScope(new TenantScope);
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

  public function bookmarks()
  {
    return $this->morphMany(Bookmark::class, 'bookmarkable');
  }
}
