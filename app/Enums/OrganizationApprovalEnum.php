<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Approved()
 * @method static static Pending()
 * @method static static Rejected()
 */
final class OrganizationApprovalEnum extends Enum
{
  const Approved = 'approved';
  const Pending = 'pending';
  const Rejected = 'rejected';
}
