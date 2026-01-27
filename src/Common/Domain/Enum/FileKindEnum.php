<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

use App\Common\Domain\Interface\EnumInterface;

enum FileKindEnum: string implements EnumInterface
{
    case USER_AVATAR_PROFILE = 'user_avatar_profile';
    case COMPANY_LOGO = 'company_logo';
    case EMPLOYEE_AGREEMENT = 'employee_agreement';
    case IMPORT_XLSX = 'import_xlsx';
    case EMAIL_ATTACHMENTS = 'email_attachments';

    public function label(): string
    {
        return match ($this) {
            self::USER_AVATAR_PROFILE => 'userAvatarProfile',
            self::COMPANY_LOGO => 'companyLogo',
            self::EMPLOYEE_AGREEMENT => 'employeeAgreement',
            self::IMPORT_XLSX => 'importXlsx',
            self::EMAIL_ATTACHMENTS => 'emailAttachments',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
