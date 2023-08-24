<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Local()
 * @method static static Google()
 */
final class UserProviderEnum extends Enum
{
  const Local = 'local';
  const Google = 'google';
}
