<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;

final readonly class ImportEmployeesPreparer
{
    public function __construct(
        private EmployeeReaderInterface $employeeReaderRepository,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function prepare(iterable $rows): array
    {
        $peselMap = [];
        $preparedRows = [];

        foreach ($rows as $row) {
            $pesel = trim((string) $row[ImportEmployeesFromXLSX::COLUMN_PESEL]);
            $existingEmployee = $this->entityReferenceCache->get(
                Employee::class,
                $pesel,
                fn (string $pesel) => $this->employeeReaderRepository->getEmployeeByPESEL($pesel)
            );

            $row[ImportEmployeesFromXLSX::COLUMN_DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS] = null !== $existingEmployee;

            if (!isset($peselMap[$pesel])) {
                $peselMap[$pesel] = $existingEmployee
                    ? EmployeeUUID::fromString($existingEmployee->getUUID()->toString())
                    : EmployeeUUID::generate();
            }

            $row[ImportEmployeesFromXLSX::COLUMN_DYNAMIC_AGGREGATE_UUID] = $peselMap[$pesel]->toString();
            $preparedRows[] = $row;
        }

        return [$preparedRows, $peselMap];
    }
}
