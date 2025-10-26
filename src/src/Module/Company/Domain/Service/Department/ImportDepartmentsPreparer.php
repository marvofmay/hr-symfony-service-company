<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;

final readonly class ImportDepartmentsPreparer
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function prepare(iterable $rows): array
    {
        $internalCodeMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $internalCode = trim((string) $row[DepartmentImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value]);
            $existingDepartment = $this->entityReferenceCache->get(
                Department::class,
                $internalCode,
                fn (string $nip) => $this->departmentReaderRepository->getDepartmentByInternalCode($internalCode)
            );

            $row[DepartmentImportColumnEnum::DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS->value] = null !== $existingDepartment;

            if (!isset($internalCodeMap[$internalCode])) {
                $internalCodeMap[$internalCode] = $existingDepartment
                    ? DepartmentUUID::fromString($existingDepartment->getUUID()->toString())
                    : DepartmentUUID::generate();
            }

            $row[DepartmentImportColumnEnum::DYNAMIC_AGGREGATE_UUID->value] = $internalCodeMap[$internalCode]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $internalCodeMap];
    }
}
