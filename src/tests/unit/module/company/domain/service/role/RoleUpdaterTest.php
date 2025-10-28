<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Common\Domain\Interface\CommandDataMapperInterface;
use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleUpdater;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;
use PHPUnit\Framework\TestCase;

class RoleUpdaterTest extends TestCase
{
    public function testItUpdatesAndSavesRole(): void
    {
        $name = 'Updated Name';
        $description = 'Updated Description';
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $command = new UpdateRoleCommand($uuid, $name, $description);

        $role = new Role();
        $role->setName('Old Name');
        $role->setDescription('Old Description');

        $reader = $this->createMock(RoleReaderInterface::class);
        $reader->method('getRoleByUUID')->with($uuid)->willReturn($role);

        $writer = $this->createMock(RoleWriterInterface::class);
        $writer->expects($this->once())
            ->method('saveRoleInDB')
            ->with($this->callback(
                fn(Role $r) => $r->getName() === $name
                    && $r->getDescription() === $description
            ));

        $mapper = $this->createMock(CommandDataMapperInterface::class);
        $mapper->method('getType')->willReturn(CommandDataMapperKindEnum::COMMAND_MAPPER_ROLE->value);
        $mapper->expects($this->once())
            ->method('map')
            ->with($role, $command)
            ->willReturnCallback(function (Role $r, UpdateRoleCommand $c) {
                $r->setName($c->name);
                $r->setDescription($c->description);
            });

        $factory = new CommandDataMapperFactory([$mapper]);

        $updater = new RoleUpdater($reader, $writer, $factory);
        $updater->update($command);
    }
}
