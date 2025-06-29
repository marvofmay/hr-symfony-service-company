<?php

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\CreateRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleCreatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateRoleCommandHandlerTest extends TestCase
{
    public function testItCallsRoleCreatorAndDispatchesEvent(): void
    {
        $name = 'Użytkownik';
        $description = 'Opis roli użytkownik ...';
        $command = new CreateRoleCommand($name, $description);

        $roleCreator = $this->createMock(RoleCreatorInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $roleCreator
            ->expects($this->once())
            ->method('create')
            ->with($name, $description);

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(
                    fn(RoleCreatedEvent $event) => $event->getData()[Role::COLUMN_NAME] === $name && $event->getData()[Role::COLUMN_DESCRIPTION] === $description
                )
            );

        $handler = new CreateRoleCommandHandler($roleCreator, $eventDispatcher);
        $handler($command);
    }
}