<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Founder()
 * @method static static Head()
 * @method static static Pastor()
 * @method static static Administrator()
 * @method static static ChurchLeader()
 * @method static static ChurchMember()
 * @method static static Other()
 */
final class RoleAtChurch extends Enum
{
  const Founder = 'Founder/Overseer';
  const Head = 'Head/Resident Pastor/Lead Pastor/Bishop';
  const Pastor = 'Pastor/Minister/Reverend';
  const Administrator = 'Administrator';
  const ChurchLeader = 'Church Leader';
  const ChurchMember = 'Church Member';
  const Other = 'Other';
}
