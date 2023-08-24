<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Enabled()
 * @method static static Disabled()
 * @method static static Suspended()
 */
final class OrganizationStatus extends Enum
{
    const Enabled = 'enabled';
    const Disabled = 'disabled';
    const Suspended = 'suspended';
}
