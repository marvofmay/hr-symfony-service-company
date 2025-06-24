<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RoleMultipleDeleter
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,  private EventDispatcherInterface $eventBus,)
    {
    }

    public function multipleDelete(Collection $roles): void
    {
        $this->roleWriterRepository->deleteMultipleRolesInDB($roles);

        $this->eventBus->dispatch(new RoleMultipleDeletedEvent(
            $roles->map(fn($role) => $role->getUUID())->toArray()
        ));
    }
}
