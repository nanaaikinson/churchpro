<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Plank\Mediable\Mediable;
use Str;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements JWTSubject
{
  use HasApiTokens, HasFactory, Notifiable, HasUlids, Mediable;

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'channels' => 'array',
    'providers' => 'array'
  ];

  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims()
  {
    return [];
  }

  // Relationships
  public function verificationCodes()
  {
    return $this->morphMany(VerificationCode::class, 'verifiable');
  }

  public function organizations()
  {
    return $this->belongsToMany(Organization::class, 'user_organizations');
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  public function prayers()
  {
    return $this->hasMany(Prayer::class);
  }

  public function branches()
  {
    return $this->belongsToMany(Branch::class, 'user_branches');
  }

  public function passwords()
  {
    return $this->hasMany(Password::class);
  }

  public function iams()
  {
    return $this->hasMany(Iam::class);
  }
}