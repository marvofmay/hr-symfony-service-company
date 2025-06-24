<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RoleUpdater
{
    public function __construct(private RoleWriterInterface $roleWriterRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function update(Role $role, string $name, ?string $description): void
    {
        $role->setName($name);
        $role->setDescription($description);
        $role->setUpdatedAt(new \DateTime());

        $this->roleWriterRepository->saveRoleInDB($role);

        $this->eventDispatcher->dispatch(new RoleUpdatedEvent($role));
    }
}
