<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RoleCreator
{
    public function __construct(private RoleWriterInterface $roleWriterRepository, private EventDispatcherInterface $eventBus)
    {
    }

    public function create(string $name, ?string $description): void
    {
        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);

        $this->roleWriterRepository->saveRoleInDB($role);

        $this->eventBus->dispatch(new RoleCreatedEvent($role));
    }
}
