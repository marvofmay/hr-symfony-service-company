<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\ImportRolesFromXLSX;
use App\Module\Company\Domain\Service\Role\RoleMultipleCreator;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class testItCreatesAndSavesMultipleRoles extends TestCase
{
    public function testItCreatesAndSavesMultipleRoles(): void
    {
        $data = [
            [
                ImportRolesFromXLSX::COLUMN_NAME => 'Admin',
                ImportRolesFromXLSX::COLUMN_DESCRIPTION => 'Administrator role',
            ],
            [
                ImportRolesFromXLSX::COLUMN_NAME => 'User',
                ImportRolesFromXLSX::COLUMN_DESCRIPTION => 'User role',
            ],
        ];

        $writer = $this->createMock(RoleWriterInterface::class);

        $writer
            ->expects($this->once())
            ->method('saveRolesInDB')
            ->with($this->callback(function (Collection $roles) use ($data) {
                if (count($roles) !== count($data)) {
                    return false;
                }

                foreach ($roles as $index => $role) {
                    if (!$role instanceof Role) {
                        return false;
                    }

                    if (
                        $role->getName() !== $data[$index][ImportRolesFromXLSX::COLUMN_NAME] ||
                        $role->getDescription() !== $data[$index][ImportRolesFromXLSX::COLUMN_DESCRIPTION]
                    ) {
                        return false;
                    }
                }

                return true;
            }));

        $creator = new RoleMultipleCreator($writer);
        $creator->multipleCreate($data);
    }
}