<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class CamelCaseConverter
{
  public static function run(array|Collection|AnonymousResourceCollection $data)
  {
    if ($data instanceof Collection) {
      return self::convertToCamelCase($data->toArray());
    }

    if ($data instanceof AnonymousResourceCollection) {
      return $data->collection->toArray();
    }

    return self::convertToCamelCase($data);
  }

  /**
   * Convert the keys of the given array to camelCase recursively.
   *
   * @param  array  $array
   * @return array
   */
  private static function convertToCamelCase(array $array)
  {
    $result = [];
    foreach ($array as $key => $value) {
      $newKey = lcfirst(str_replace('_', '', ucwords($key, '_')));

      if (is_array($value)) {
        $value = self::convertToCamelCase($value);
      } elseif ($value instanceof Collection) {
        $value = self::convertToCamelCase($value->toArray());
      }

      $result[$newKey] = $value;
    }
    return $result;
  }
}