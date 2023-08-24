<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Active()
 * @method static static Suspended()
 */
final class UserStatusEnum extends Enum
{
  const Active = 'active';
  const Suspended = 'suspended';
}
