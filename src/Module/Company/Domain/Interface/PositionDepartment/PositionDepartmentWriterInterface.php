<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\PositionDepartment;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Position;

interface PositionDepartmentWriterInterface
{
    public function deletePositionDepartmentByPositionInDB(Position $position, Department $department, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void;

    public function deletePositionDepartmentsByPositionInDB(Position $position, DeleteTypeEnum $deleteTypeEnum): void;
}
