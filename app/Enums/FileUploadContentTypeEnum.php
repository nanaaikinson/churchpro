<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Avatar()
 * @method static static Logo()
 * @method static static Excerpt()
 */
final class FileUploadContentTypeEnum extends Enum
{
  const Avatar = 'avatar';
  const Logo = 'logo';
  const Excerpt = 'excerpt';
}
