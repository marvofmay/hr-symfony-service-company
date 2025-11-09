<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\domain\service\role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\Company\Domain\Service\Role\RoleMultipleCreator;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class testItCreatesAndSavesMultipleRoles extends TestCase
{
    public function testItCreatesAndSavesMultipleRoles(): void
    {
        $data = [
            [
                RoleImportColumnEnum::ROLE_NAME->value => 'Admin',
                RoleImportColumnEnum::ROLE_DESCRIPTION->value => 'Administrator role',
            ],
            [
                RoleImportColumnEnum::ROLE_NAME->value => 'User',
                RoleImportColumnEnum::ROLE_DESCRIPTION->value => 'User role',
            ],
        ];

        $writer = $this->createMock(RoleWriterInterface::class);

        $writer
            ->expects($this->once())
            ->method('saveRoles')
            ->with($this->callback(function (Collection $roles) use ($data) {
                if (count($roles) !== count($data)) {
                    return false;
                }

                foreach ($roles as $index => $role) {
                    if (!$role instanceof Role) {
                        return false;
                    }

                    if (
                        $role->getName() !== $data[$index][RoleImportColumnEnum::ROLE_NAME->value]
                        || $role->getDescription() !== $data[$index][RoleImportColumnEnum::ROLE_DESCRIPTION->value]
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
