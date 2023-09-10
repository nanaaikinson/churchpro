<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Web()
 * @method static static Android()
 * @method static static Ios()
 */
final class DeviceTypeEnum extends Enum
{
  const Web = 'web';
  const Android = 'android';
  const Ios = 'ios';
}
