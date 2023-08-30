<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

  public function newUniqueId(): string
  {
    return ((string) Str::ulid());
  }

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims(): array
  {
    return [];
  }

  // Accessors
  public function name(): Attribute
  {
    return Attribute::make(
      get: fn() => $this->first_name . ' ' . $this->last_name,
    );
  }

  // Relationships
  public function verificationCodes(): MorphMany
  {
    return $this->morphMany(VerificationCode::class, 'verifiable');
  }

  public function organizations(): BelongsToMany
  {
    return $this->belongsToMany(Organization::class, 'user_organizations');
  }

  public function comments(): HasMany
  {
    return $this->hasMany(Comment::class);
  }

  public function prayers(): HasMany
  {
    return $this->hasMany(Prayer::class);
  }

  public function branches(): BelongsToMany
  {
    return $this->belongsToMany(Branch::class, 'user_branches');
  }

  public function passwords(): HasMany
  {
    return $this->hasMany(Password::class);
  }

  public function iams(): HasMany
  {
    return $this->hasMany(Iam::class);
  }

  public function devices(): HasMany
  {
    return $this->hasMany(UserDevice::class);
  }
}