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
            $row['_is_department_already_exists_with_internal_code'] = null !== $existingDepartment;

            if (!isset($internalCodeMap[$internalCode])) {
                $internalCodeMap[$internalCode] = $existingDepartment
                    ? DepartmentUUID::fromString($existingDepartment->getUUID()->toString())
                    : DepartmentUUID::generate();
            }

            $row['_aggregate_uuid'] = $internalCodeMap[$internalCode]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $internalCodeMap];
    }
}