<?php

namespace App\tests\unit\module\company\application\commandHandler;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\CommandHandler\Role\CreateRoleCommandHandler;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;

;
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
            ->with(
                $this->equalTo($command->name),
                $this->equalTo($command->description)
            );

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function (RoleCreatedEvent $event) use ($name, $description) {
                    $data = $event->getData();
                    return $data[CreateRoleCommand::ROLE_NAME] === $name
                        && $data[CreateRoleCommand::ROLE_DESCRIPTION] === $description;
                })
            );

        $validators = [];

        $handler = new CreateRoleCommandHandler($roleCreator, $eventDispatcher, $validators);
        $handler($command);
    }
}
