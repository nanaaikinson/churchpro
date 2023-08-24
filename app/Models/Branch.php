<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * @mixin IdeHelperBranch
 */
class Branch extends Model
{
  use HasFactory, HasUlids;

  // Casts
  protected $casts = [
    'address' => 'array',
    'contact' => 'array',
  ];

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  // Relationships
  public function organization()
  {
    return $this->belongsTo(Organization::class);
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'user_branches');
  }
}