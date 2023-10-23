<?php

namespace Database\Factories;

use App\Enums\FileUploadContentTypeEnum;
use App\Enums\OrganizationApprovalEnum;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class OrganizationFactory extends Factory
{
  protected $model = Organization::class;

  public function definition()
  {
    return [
      'name' => fake()->unique()->company(),
      'approval' => fake()->randomElement(OrganizationApprovalEnum::getValues()),
      'status' => fake()->randomElement(OrganizationStatus::getValues()),
      'data' => json_encode([
        'phone_number' => fake()->phoneNumber(),
        'location' => fake()->address(),
        'email' => fake()->email(),
        'about' => fake()->text(),
        'description' => fake()->paragraph(),
        'socials' => [
          'facebook' => 'https://facebook.com',
          'twitter' => 'https://twitter.com',
          'instagram' => 'https://instagram.com',
          'youtube' => 'https://youtube.com',
          'tiktok' => 'https://tiktok.com',
          'threads' => 'https://threads.com',
          'website' => 'https://website.com',
        ],
      ]),
    ];
  }

  public function configure(): static
  {
    return $this->afterCreating(function (Organization $item) {
      $type = fake()->randomElement(['shapes', 'icons', 'adventurer']);
      $url = "https://api.dicebear.com/7.x/{$type}/png";
      $image = Image::make($url)->encode('png');
      $media = FileService::uploadFromString($image, FileUploadContentTypeEnum::Logo);

      $item->attachMedia($media, 'logo');
    });
  }
}
