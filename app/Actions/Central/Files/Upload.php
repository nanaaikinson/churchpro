<?php

namespace App\Actions\Central\Files;

use App\Enums\FileUploadContentTypeEnum;
use App\Services\FileService;
use App\Traits\ApiResponse;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Validation\Rules\File;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class Upload
{
  use AsAction, ApiResponse;

  public function rules(): array
  {
   $mimes = ['jpg', 'jpeg', 'png'];
   $size = 3 * 1024;

    return [
      'file' => ['required', File::types($mimes)->max($size)],
      'content_type' => ['required', new EnumValue(FileUploadContentTypeEnum::class)]
    ];
  }

  public function handle(ActionRequest $request): \Illuminate\Http\JsonResponse
  {
    $contentType = request()->input('content_type');

    try {
      $media = FileService::uploadFromSource($request->file('file'), $contentType);

      if (!$media) {
        throw new \Exception('Failed to upload file.');
      }

      return $this->dataResponse([
        'id' => $media->id,
        'url' => FileService::getFileUrlFromMedia($media),
      ]);
    }
    catch (\Exception $e) {
      return $this->badRequestResponse(null, $e->getMessage());
    }
  }
}
