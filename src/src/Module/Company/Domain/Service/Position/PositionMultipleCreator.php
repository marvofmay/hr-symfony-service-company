<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class PositionMultipleCreator
{
    public function __construct(private PositionWriterInterface $positionWriterRepository, private DepartmentReaderInterface $departmentReaderRepository,)
    {
    }

    public function multipleCreate(array $data): void
    {
        $positions = new ArrayCollection();
        foreach ($data as $item) {
            $position = new Position();
            $position->setName($item[ImportPositionsFromXLSX::COLUMN_NAME]);
            $position->setDescription($item[ImportPositionsFromXLSX::COLUMN_DESCRIPTION]);
            $position->setActive((bool)$item[ImportPositionsFromXLSX::COLUMN_ACTIVE]);

            $departments = $this->departmentReaderRepository->getDepartmentsByUUID([$item[ImportPositionsFromXLSX::COLUMN_DEPARTMENT_UUID]]);
            foreach ($departments as $department) {
                $position->addDepartment($department);
            }

            $positions[] = $position;
        }

        $this->positionWriterRepository->savePositionsInDB($positions);
    }
}