<?php

namespace App\tests\unit\module\company\entity;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\PositionDepartment;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $position = new Position();

        $position->setName('Manager');
        $this->assertSame('Manager', $position->getName());

        $position->setDescription('Responsible for managing the team');
        $this->assertSame('Responsible for managing the team', $position->getDescription());

        $position->setActive(true);
        $this->assertTrue($position->getActive());
    }

    public function testEmployeesCollectionIsEmptyOnInit(): void
    {
        $position = new Position();
        $this->assertCount(0, $position->getEmployees());
    }

    public function testAddDepartment(): void
    {
        $department = $this->createMock(Department::class);
        $position = new Position();

        $position->addDepartment($department);

        $this->assertCount(1, $position->getPositionDepartments());
        $this->assertSame($department, $position->getDepartments()->first());
    }

    public function testAddDepartmentDoesNotAddDuplicate(): void
    {
        $department = $this->createMock(Department::class);

        $mockPositionDepartment = $this->createMock(PositionDepartment::class);
        $mockPositionDepartment->method('getDepartment')->willReturn($department);

        $position = new Position();
        $position->getPositionDepartments()->add($mockPositionDepartment);

        $position->addDepartment($department);

        $this->assertCount(1, $position->getPositionDepartments());
    }
}