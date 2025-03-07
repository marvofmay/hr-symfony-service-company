<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportEmployeesFromXLSX extends XLSXIterator
{
    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): ?string
    {
        [$uuid, $firstName, $lastName, $parentEmployeeUUID, $active] = $row + [null, null, null, null, true];

        if ($errorMessage = $this->validateEmployeeFirstAndLastName($firstName, 'firstName')) {
            return $errorMessage;
        }

        if ($errorMessage = $this->validateEmployeeFirstAndLastName($lastName, 'lastName')) {
            return $errorMessage;
        }

        if (!$this->isEmployeeWithUUIDExists($uuid, 'employeeUUID')) {
            return $this->formatErrorMessage('employee.uuid.notExists');
        }

        if (!$this->isEmployeeWithUUIDExists($parentEmployeeUUID, 'parentEmployeeUUID')) {
            return $this->formatErrorMessage('employee.uuid.notExists');
        }

        //ToDo: add validation is exist ParentEmployeeByUUID

        return null;
    }

    private function validateEmployeeFirstAndLAstName(?string $firstName, $kind): ?string
    {
        if (empty($firstName)) {
            return $this->formatErrorMessage('employee.firstName.required');
        }

        if (strlen($firstName) < 3) {
            return $this->formatErrorMessage('employee.firstName.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function isEmployeeWithUUIDExists(string $employeeUUID): bool
    {
        return $this->employeeReaderRepository->isEmployeeWithUUIDExists($employeeUUID);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = []): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, 'employees'),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
