<?php

namespace App\tests\unit\module\company\domain\entity;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\PositionDepartment;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $position = new Position();

        $position->name = 'Manager';
        $this->assertSame('Manager', $position->name);

        $position->description = 'Responsible for managing the team';
        $this->assertSame('Responsible for managing the team', $position->description);

        // $position->setActive(true);
        $this->assertTrue($position->active);
    }

    public function testEmployeesCollectionIsEmptyOnInit(): void
    {
        $position = new Position();
        $this->assertCount(0, $position->employees);
    }

    public function testAddDepartment(): void
    {
        $department = $this->createMock(Department::class);
        $position = new Position();

        $position->addDepartment($department);

        $this->assertCount(1, $position->positionDepartments);
        $this->assertSame($department, $position->getDepartments()->first());
    }

    public function testAddDepartmentDoesNotAddDuplicate(): void
    {
        $department = $this->createMock(Department::class);

        $mockPositionDepartment = $this->createMock(PositionDepartment::class);
        $mockPositionDepartment->department = $department;

        $position = new Position();
        $position->positionDepartments->add($mockPositionDepartment);

        $position->addDepartment($department);
        var_dump(count($position->positionDepartments));

        $this->assertCount(1, $position->positionDepartments);
    }
}
