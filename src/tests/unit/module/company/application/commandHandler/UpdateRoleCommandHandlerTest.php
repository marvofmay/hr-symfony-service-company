<?php

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\UpdateRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateRoleCommandHandlerTest extends TestCase
{
    public function testItUpdatesRoleAndDispatchesEvent(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $name = 'Użytkownik';
        $description = 'Użytkownik ...';

        $command = new UpdateRoleCommand($uuid, $name, $description);

        $role = new Role();
        $roleReader = $this->createMock(RoleReaderInterface::class);
        $roleReader->expects($this->once())
            ->method('getRoleByUUID')
            ->with($uuid)
            ->willReturn($role);

        $roleUpdater = $this->createMock(RoleUpdaterInterface::class);
        $roleUpdater->expects($this->once())
            ->method('update')
            ->with(
                $this->identicalTo($role),
                $this->equalTo($name),
                $this->equalTo($description)
            );

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($uuid, $name, $description) {
                if (!$event instanceof RoleUpdatedEvent) {
                    return false;
                }

                $data = $event->getData();

                return $data[UpdateRoleCommand::ROLE_UUID] === $uuid
                    && $data[UpdateRoleCommand::ROLE_NAME] === $name
                    && $data[UpdateRoleCommand::ROLE_DESCRIPTION] === $description;
            }));

        $handler = new UpdateRoleCommandHandler(
            $roleReader,
            $roleUpdater,
            $eventDispatcher,
            []
        );

        $handler($command);

        $this->assertTrue(true);
    }
}
