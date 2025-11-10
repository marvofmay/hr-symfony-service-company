<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Email;

use Ramsey\Uuid\UuidInterface;

final readonly class SendEmailCommand
{
    public const string EMAIL_UUID = 'emailUUID';

    public function __construct(public UuidInterface $emailUUID) {}
}