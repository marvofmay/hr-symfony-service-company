<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Module\Company\Application\Command\Role\DeleteRoleCommand;

readonly class DeleteRoleCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function __invoke(DeleteRoleCommand $command): void
    {
        $role = $command->getRole();
        $role->setDeletedAt(new DateTime());
        $this->entityManager->flush();
    }
}