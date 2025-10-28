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

        $command = new UpdateRoleCommand($roleMock->getUUID()->toString(), $name, $description);

        $this->assertSame($name, $command->name);
        $this->assertSame($description, $command->description);
    }

    public function testItAllowsNullDescription(): void
    {
        $name = 'Użytkownik';
        $roleMock = $this->createMock(Role::class);

        $command = new UpdateRoleCommand($roleMock->getUUID()->toString(), $name, null);

        $this->assertSame($name, $command->name);
        $this->assertNull($command->description);
    }
}
