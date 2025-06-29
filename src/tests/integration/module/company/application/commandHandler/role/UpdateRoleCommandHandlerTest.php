<?php

declare(strict_types=1);

namespace App\tests\integration\module\company\application\commandHandler\role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\UpdateRoleCommandHandler;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\tests\functional\FunctionalTestBase;

class UpdateRoleCommandHandlerTest extends FunctionalTestBase
{
    public function testItUpdatesRole(): void
    {
        $this->getAuthenticatedClient();

        $container = self::getContainer();

        $handler = $container->get(UpdateRoleCommandHandler::class);
        $repoReader = $container->get(RoleReaderInterface::class);
        $repoWriter = $container->get(RoleWriterInterface::class);

        $roleName = 'Menadżer';
        $roleDescription = $roleName . ' ...';

        $role = new Role();
        $role->setName($roleName);
        $role->setDescription($roleDescription);

        $repoWriter->saveRoleInDB($role);

        $newRoleName = $roleName . ' updated';
        $newRoleDescription = $roleDescription . ' updated';

        $command = new UpdateRoleCommand($newRoleName, $newRoleDescription, $role);
        $handler($command);

        $role = $repoReader->getRoleByUuid($role->getUUID()->toString());

        $this->assertNotNull($role);
        $this->assertSame($newRoleName, $role->getName());
        $this->assertSame($newRoleDescription, $role->getDescription());
    }
}