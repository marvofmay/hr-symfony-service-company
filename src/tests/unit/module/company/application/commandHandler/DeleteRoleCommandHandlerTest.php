<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\DeleteRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleDeleterInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DeleteRoleCommandHandlerTest extends TestCase
{
    public function testItDeletesRoleAndDispatchesEvent(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $command = new DeleteRoleCommand($uuid);

        $role = new Role();
        $role->setName('Test');
        $role->setDescription('To delete');
        $role->setCreatedAt();

        $roleReader = $this->createMock(RoleReaderInterface::class);
        $roleReader
            ->expects($this->once())
            ->method('getRoleByUUID')
            ->with($uuid)
            ->willReturn($role);

        $roleDeleter = $this->createMock(RoleDeleterInterface::class);
        $roleDeleter
            ->expects($this->once())
            ->method('delete')
            ->with($role);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(
                fn (RoleDeletedEvent $event) =>
                    $event->getData()[DeleteRoleCommand::ROLE_UUID] === $uuid
            ));

        $validators = [];

        $handler = new DeleteRoleCommandHandler(
            $roleReader,
            $roleDeleter,
            $eventDispatcher,
            $validators
        );

        $handler($command);

        $this->assertTrue(true);
    }
}
