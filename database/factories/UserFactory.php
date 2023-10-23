<?php

namespace Database\Factories;

use App\Enums\UserChannelEnum;
use App\Enums\UserProviderEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $gender = fake()->randomElement(['male', 'female']);
    $firstName = $gender == 'male' ? fake()->firstNameMale() : fake()->firstNameFemale();
    $signUpProvider = fake()->randomElement(UserProviderEnum::getValues());
    $password = $signUpProvider == UserProviderEnum::Local
      ? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' // Password
      : null;

    return [
      'first_name' => $firstName,
      'last_name' => fake()->lastName(),
      'email' => fake()->unique()->safeEmail(),
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
      'password' => $password,
      'status' => fake()->randomElement(UserStatusEnum::getValues()),
      'channels' => json_encode([UserChannelEnum::Web, UserChannelEnum::Mobile]),
      'sign_up_provider' => $signUpProvider,
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   */
  public function unverified(): static
  {
    return $this->state(fn (array $attributes) => [
      'email_verified_at' => null,
    ]);
  }
}
