<?php

namespace App\Services;

use App\Enums\FileUploadContentTypeEnum;
use App\Models\Media;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Plank\Mediable\Facades\MediaUploader;

class FileService
{
  /**
   * @param mixed $file
   * @param string $contentType
   * @param string $disk
   *
   * @return \Plank\Mediable\Media
   */
  public static function uploadFromSource(mixed $file, string $contentType, string $disk = 's3')
  {
    [$filename, $path] = self::getPathAndFilename($contentType);

    return MediaUploader::fromSource($file)
      ->useFilename($filename)
      ->toDirectory($path)
      ->toDisk($disk)
      ->upload();
  }

  /**
   * @param string $source
   * @param string $contentType
   * @param string $disk
   *
   * @return \Plank\Mediable\Media
   */
  public static function uploadFromString(string $source, string $contentType, string $disk = 's3')
  {
    $file = Image::make($source)->encode('jpg');
    [$filename, $path] = self::getPathAndFilename($contentType);

    return MediaUploader::fromString($file)
      ->useFilename($filename)
      ->toDirectory($path)
      ->toDisk($disk)
      ->upload();
  }

  /**
   * @param string $key
   * @param string $disk
   *
   * @return string
   */
  public static function generateUrl(string $key, string $disk = "s3"): string
  {
    return Storage::disk($disk)->temporaryUrl($key, now()->addDay());
  }

  /**
   * @param string $key
   * @param string $disk
   *
   * @return string
   */
  public static function getFileUrlFromCache(string $key, $disk = "s3")
  {
    $value = Cache::remember($key, now()->addDay(), function () use ($key, $disk) {
      return self::generateUrl($key, $disk);
    });

    return $value;
  }

  /**
   * @param \App\Models\Media|null $media
   *
   * @return string|null
   */
  public static function getFileUrlFromMedia(Media|null $media)
  {
    if (!$media) {
      return null;
    }

    $key = "{$media->getDiskPath()}";

    return self::getFileUrlFromCache($key);
  }

  public static function deleteFileFromCache(string|Media $key)
  {
    $key = $key instanceof Media ? $key->getDiskPath() : $key;

    Cache::forget($key);
  }

  /**
   * @param string $contentType
   *
   * @return array
   */
  public static function getPathAndFilename(string $contentType)
  {
    $filename = now()->timestamp . '_' . (string) Str::ulid(now()->toDate());

    $path = match ($contentType) {
      FileUploadContentTypeEnum::Avatar => 'uploads/avatars',
      FileUploadContentTypeEnum::Logo => 'uploads/logos',
      FileUploadContentTypeEnum::Excerpt => 'uploads/excerpts'
    };

    return [
      'filename' => $filename,
      'path' => $path,
    ];
  }
}