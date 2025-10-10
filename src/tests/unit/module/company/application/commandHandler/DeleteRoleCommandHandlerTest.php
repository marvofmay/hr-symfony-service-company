<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\DeleteRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleDeleterInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DeleteRoleCommandHandlerTest extends TestCase
{
    public function testItDeletesRoleAndDispatchesEvent(): void
    {
        $uuid = Uuid::fromString('123e4567-e89b-12d3-a456-426614174000');

        $role = $this->createMock(Role::class);
        $role->expects($this->once())
            ->method('getUUID')
            ->willReturn($uuid);

        $command = new DeleteRoleCommand($role);

        $roleDeleter = $this->createMock(RoleDeleterInterface::class);
        $roleDeleter->expects($this->once())
            ->method('delete')
            ->with($role);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn (RoleDeletedEvent $event) => $event->getData()[Role::COLUMN_UUID] === $uuid));

        $handler = new DeleteRoleCommandHandler($roleDeleter, $eventDispatcher);

        $handler($command);
    }
}
