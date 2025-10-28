<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;

final class ImportPositionsReferenceLoader
{
    public array $positions = [] {
        get {
            return $this->positions;
        }
    }
    public array $departments = [] {
        get {
            return $this->departments;
        }
    }

    public function __construct(
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly PositionReaderInterface $positionReaderRepository,
    ) {
    }

    public function preload(array $rows): void
    {
        $positionNames = [];
        $departmentInternalCodes = [];

        foreach ($rows as $row) {
            if (!empty($row[PositionImportColumnEnum::POSITION_NAME->value])) {
                $positionNames[] = trim((string) $row[PositionImportColumnEnum::POSITION_NAME->value]);
            }
            if (!empty($row[PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value])) {
                $departmentInternalCodes[] = trim((string) $row[PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value]);
            }
        }

        $positionNames = array_unique($positionNames);
        $departmentInternalCodes = array_unique($departmentInternalCodes);

        $this->positions = $this->mapByName($this->positionReaderRepository->getPositionsByNames($positionNames));
        $this->departments = $this->mapByInternalCode($this->departmentReaderRepository->getDepartmentsByInternalCode($departmentInternalCodes));
    }

    private function mapByName(iterable $positions): array
    {
        $map = [];
        foreach ($positions as $position) {
            $map[trim($position->getName())] = $position;
        }

        return $map;
    }

    private function mapByInternalCode(iterable $departments): array
    {
        $map = [];
        foreach ($departments as $department) {
            $map[trim($department->getInternalCode())] = $department;
        }

        return $map;
    }
}
