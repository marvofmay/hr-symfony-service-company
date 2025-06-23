<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class RoleAccessCreator
{
    public function __construct(private RoleWriterInterface $roleWriterRepository, private EventDispatcherInterface $eventBus)
    {
    }

    public function create(Role $role, Collection $accesses): void
    {
        foreach ($accesses as $access) {
            $role->addAccess($access);
        }

        $this->roleWriterRepository->saveRoleInDB($role);

        $this->eventBus->dispatch(new RoleAssignedAccessesEvent($role));
    }
}
