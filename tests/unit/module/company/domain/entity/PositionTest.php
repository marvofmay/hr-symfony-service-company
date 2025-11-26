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
        $position = Position::create('Manager', 'Responsible for managing the team', true);
        $this->assertSame('Manager', $position->getName());
        $this->assertSame('Responsible for managing the team', $position->getDescription());
        $this->assertTrue($position->isActive());
    }

    public function testEmployeesCollectionIsEmptyOnInit(): void
    {
        $position = Position::create('Manager', 'Responsible for managing the team', true);
        $this->assertCount(0, $position->getEmployees());
    }

    public function testAddDepartment(): void
    {
        $department = $this->createMock(Department::class);
        $position = Position::create('Manager', 'Responsible for managing the team', true);

        $position->addDepartment($department);

        $this->assertCount(1, $position->getPositionDepartments());
        $this->assertSame($department, $position->getDepartments()->first());
    }

    public function testAddDepartmentDoesNotAddDuplicate(): void
    {
        $department = $this->createMock(Department::class);

        $mockPositionDepartment = $this->createMock(PositionDepartment::class);
        $mockPositionDepartment->department = $department;

        $position = Position::create('Manager', 'Responsible for managing the team', true);
        $position->getPositionDepartments()->add($mockPositionDepartment);

        $position->addDepartment($department);
        var_dump(count($position->getPositionDepartments()));

        $this->assertCount(1, $position->getPositionDepartments());
    }
}
