<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static AccountCreation()
 * @method static static AccountVerification()
 * @method static static TenantOnboarding()
 * @method static static Complete()
 */
final class UserOnboardingStepEnum extends Enum
{
  const AccountCreation = 'account creation';
  const AccountVerification = 'account verification';
  const TenantOnboarding = 'tenant onboarding';
  const Complete = 'complete';
}
