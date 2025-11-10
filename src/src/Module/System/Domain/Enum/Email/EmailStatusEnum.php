<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum\Email;

enum EmailStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}