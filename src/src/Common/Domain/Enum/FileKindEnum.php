<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum FileKindEnum: string implements EnumInterface
{
    case USER_PHOTO_PROFILE = 'user_photo_profile';
    case COMPANY_LOGO       = 'company_logo';
    case EMPLOYEE_AGREEMENT = 'employee_agreement';

    public function label(): string
    {
        return match ($this) {
            self::USER_PHOTO_PROFILE => 'userPhotoProfile',
            self::COMPANY_LOGO       => 'companyLogo',
            self::EMPLOYEE_AGREEMENT => 'employeeAgreement',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
