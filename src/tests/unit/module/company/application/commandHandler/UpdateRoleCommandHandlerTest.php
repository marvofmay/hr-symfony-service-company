<?php

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\UpdateRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UpdateRoleCommandHandlerTest extends TestCase
{
    public function testItUpdatesRoleAndDispatchesEvent(): void
    {
        $name = 'Użytkownik';
        $description = 'Użytkownik ...';
        $uuid =  Uuid::fromString('123e4567-e89b-12d3-a456-426614174000');

        $role = $this->createMock(Role::class);
        $role->method('getUUID')->willReturn($uuid);

        $command = new UpdateRoleCommand($name, $description, $role);

        $roleUpdater = $this->createMock(RoleUpdaterInterface::class);
        $roleUpdater
            ->expects($this->once())
            ->method('update')
            ->with($role, $name, $description);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(
                fn(RoleUpdatedEvent $event) =>
                    $event->getData()[Role::COLUMN_UUID] === $uuid &&
                    $event->getData()[Role::COLUMN_NAME] === $name &&
                    $event->getData()[Role::COLUMN_DESCRIPTION] === $description
            ));

        $handler = new UpdateRoleCommandHandler($roleUpdater, $eventDispatcher);

        $handler($command);
    }
}