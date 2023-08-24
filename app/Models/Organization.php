<?php

namespace App\Models;

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
}