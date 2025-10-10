<?php

declare(strict_types=1);

namespace App\tests\integration\module\company\application\commandHandler\role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\CreateRoleCommandHandler;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\tests\functional\FunctionalTestBase;

class CreateRoleCommandHandlerTest extends FunctionalTestBase
{
    public function testItCreatesRole(): void
    {
        $this->getAuthenticatedClient();

        $container = self::getContainer();

        $handler = $container->get(CreateRoleCommandHandler::class);
        $repo = $container->get(RoleReaderInterface::class);

        $roleName = 'Moderator';
        $roleDescription = $roleName.' ...';

        $command = new CreateRoleCommand($roleName, $roleDescription);
        $handler($command);

        $role = $repo->getRoleByName($roleName);

        $this->assertNotNull($role);
        $this->assertSame($roleDescription, $role->getDescription());
    }
}
