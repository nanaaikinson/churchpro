<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Plank\Mediable\Media as PlankMedia;
use Str;

class Media extends PlankMedia
{
  use HasUlids;

  /**
   * Generate a new ULID for the model.
   *
   * @return string
   */
  public function newUniqueId()
  {
    return ((string) Str::ulid());
  }
}