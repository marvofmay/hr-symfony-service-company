<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\AuthEvent;

use App\Module\System\Domain\Entity\AuthEvent;

interface AuthEventWriterInterface
{
    public function saveAuthEventInDB(AuthEvent $authEvent): void;
}
