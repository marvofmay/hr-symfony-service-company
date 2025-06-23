<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Event\Role\RoleImportedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class RoleMultipleCreator
{
    public function __construct(private RoleWriterInterface $roleWriterRepository,  private EventDispatcherInterface $eventBus,)
    {
    }

    public function multipleCreate(array $data): void
    {
        $roles = new ArrayCollection();
        foreach ($data as $item) {
            $role = new Role();
            $role->setName($item[ImportRolesFromXLSX::COLUMN_NAME]);
            $role->setDescription($item[ImportRolesFromXLSX::COLUMN_DESCRIPTION]);

            $roles[] = $role;
        }

        $this->roleWriterRepository->saveRolesInDB($roles);

        $this->eventBus->dispatch(new RoleImportedEvent($roles));
    }
}
