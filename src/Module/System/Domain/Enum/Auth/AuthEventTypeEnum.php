<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Auth;

enum AuthEventTypeEnum: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case TOKEN_REVOKED = 'token_revoked';
    case TOKEN_EXPIRED = 'token_expired';
    case FAILED_LOGIN = 'failed_login';

    public function label(): string
    {
        return match ($this) {
            self::LOGIN => 'login',
            self::LOGOUT => 'logout',
            self::TOKEN_REVOKED => 'revoked',
            self::TOKEN_EXPIRED => 'expired',
            self::FAILED_LOGIN => 'failed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}