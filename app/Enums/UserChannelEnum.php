<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static Web()
 * @method static static Mobile()
 */
final class UserChannelEnum extends Enum
{
  const Admin = 'admin';
  const Web = 'web';
  const Mobile = 'mobile'; // TODO: Think through this before removal
}
