<?php

declare(strict_types=1);

namespace App\tests\integration\module\company\application\command;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use PHPUnit\Framework\TestCase;

class UpdateRoleCommandTest extends TestCase
{
    public function testItStoresAndReturnsNameDescriptionAndRole(): void
    {
        $name = 'Moderator';
        $description = 'Opis ....';

        $roleMock = $this->createMock(Role::class);

        $command = new UpdateRoleCommand($name, $description, $roleMock);

        $this->assertSame($name, $command->getName());
        $this->assertSame($description, $command->getDescription());
        $this->assertSame($roleMock, $command->getRole());
    }

    public function testItAllowsNullDescription(): void
    {
        $name = 'Użytkownik';
        $roleMock = $this->createMock(Role::class);

        $command = new UpdateRoleCommand($name, null, $roleMock);

        $this->assertSame($name, $command->getName());
        $this->assertNull($command->getDescription());
        $this->assertSame($roleMock, $command->getRole());
    }
}
