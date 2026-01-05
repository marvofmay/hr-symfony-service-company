<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleCreator;
use PHPUnit\Framework\TestCase;

final class RoleCreatorTest extends TestCase
{
    public function testItCreatesAndSavesRole(): void
    {
        $command = new CreateRoleCommand('User', 'User ...');

        $writer = $this->createMock(RoleWriterInterface::class);
        $writer->expects($this->once())
            ->method('saveRole')
            ->with($this->callback(
                fn (Role $role) =>
                $role->getName() === 'User' &&
                $role->getDescription() === 'User ...'
            ));

        $creator = new RoleCreator($writer);
        $creator->create($command->name, $command->description);
    }
}
