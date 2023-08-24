<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Approved()
 * @method static static Pending()
 * @method static static Rejected()
 */
final class OrganizationApproval extends Enum
{
    const Approval = 'approval';
    const Pending = 'pending';
    const Rejected = 'rejected';
}
