<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Common\Domain\Interface\CommandDataMapperInterface;
use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleCreator;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;
use PHPUnit\Framework\TestCase;

final class RoleCreatorTest extends TestCase
{
    public function testItCreatesAndSavesRole(): void
    {
        $command = new CreateRoleCommand('User', 'User ...');

        $writer = $this->createMock(RoleWriterInterface::class);
        $writer->expects($this->once())
            ->method('saveRoleInDB')
            ->with($this->callback(fn(Role $role) =>
                $role->getName() === 'User' &&
                $role->getDescription() === 'User ...'
            ));


        $mapper = $this->createMock(CommandDataMapperInterface::class);
        $mapper->method('getType')->willReturn(CommandDataMapperKindEnum::COMMAND_MAPPER_ROLE->value);
        $mapper->expects($this->once())
            ->method('map')
            ->with($this->isInstanceOf(Role::class), $command)
            ->willReturnCallback(function (Role $role, CreateRoleCommand $cmd) {
                $role->setName($cmd->name);
                $role->setDescription($cmd->description);
            });

        $factory = new CommandDataMapperFactory([$mapper]);

        $creator = new RoleCreator($writer, $factory);
        $creator->create($command);
    }
}
