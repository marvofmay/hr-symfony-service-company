<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Email;

use App\Module\System\Domain\Entity\Email;

interface EmailSenderInterface
{
    public function send(Email $email, ?string $template = null, array $context = []): void;
}
