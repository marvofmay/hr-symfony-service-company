<?php

declare(strict_types=1);

namespace App\tests\integration\module\company\application\command;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use PHPUnit\Framework\TestCase;

class CreateRoleCommandTest extends TestCase
{
    public function testItStoresNameAndDescription(): void
    {
        $command = new CreateRoleCommand('Administrator', 'Rola - Administrator');

        $this->assertSame('Administrator', $command->getName());
        $this->assertSame('Rola - Administrator', $command->getDescription());
    }

    public function testItCanHandleNullDescription(): void
    {
        $command = new CreateRoleCommand('Użytkownik', null);

        $this->assertSame('Użytkownik', $command->getName());
        $this->assertNull($command->getDescription());
    }
}