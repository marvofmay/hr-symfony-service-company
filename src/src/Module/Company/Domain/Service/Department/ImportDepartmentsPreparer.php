<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;

final readonly class ImportDepartmentsPreparer
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
    ) {}

    public function prepare(iterable $rows): array
    {
        $internalCodeMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $internalCode = trim((string) $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_INTERNAL_CODE]);

            $existingDepartment = $this->departmentReaderRepository->getDepartmentByInternalCode($internalCode);
            $row[ImportDepartmentsFromXLSX::COLUMN_DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS] = null !== $existingDepartment;

            if (!isset($internalCodeMap[$internalCode])) {
                $internalCodeMap[$internalCode] = $existingDepartment
                    ? DepartmentUUID::fromString($existingDepartment->getUUID()->toString())
                    : DepartmentUUID::generate();
            }

            $row[ImportDepartmentsFromXLSX::COLUMN_DYNAMIC_AGGREGATE_UUID] = $internalCodeMap[$internalCode]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $internalCodeMap];
    }
}