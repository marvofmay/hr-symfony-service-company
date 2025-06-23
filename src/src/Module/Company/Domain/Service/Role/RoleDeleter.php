<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RoleDeleter
{
    public function __construct(private RoleWriterInterface $roleWriterRepository, private EventDispatcherInterface $eventBus)
    {
    }

    public function delete(Role $role): void
    {
        $this->roleWriterRepository->deleteRoleInDB($role);

        $this->eventBus->dispatch(new RoleDeletedEvent($role));
    }
}
