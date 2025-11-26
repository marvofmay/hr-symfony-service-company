<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\EventLog;

use Symfony\Component\Security\Core\User\UserInterface;

interface EventLogCreatorInterface
{
    public function create(string $eventClass, string $entityClass, string $jsonData, ?UserInterface $user): void;
}
