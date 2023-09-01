<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Str;

/**
 * @mixin IdeHelperOrganization
 */
class Organization extends Model
{
  use HasFactory, HasUlids, Mediable;

  // Casts
  protected $casts = [
    'data' => 'array',
    'settings' => 'array',
  ];

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  public function logo(): Attribute
  {
    return Attribute::make(get: fn() => FileService::getFileUrlFromMedia($this->firstMedia('logo')));
  }

  // Relationships
  public function users()
  {
    return $this->belongsToMany(User::class, 'user_organizations');
  }

  public function events()
  {
    return $this->hasMany(Event::class);
  }

  public function prayers()
  {
    return $this->hasMany(Prayer::class);
  }

  public function branches()
  {
    return $this->hasMany(Branch::class);
  }

  public function iams()
  {
    return $this->hasMany(Iam::class);
  }

  public function bookmarks()
  {
    return $this->morphMany(Bookmark::class, 'bookmarkable');
  }

  public function likes()
  {
    return $this->morphMany(Like::class, 'likeable');
  }
}