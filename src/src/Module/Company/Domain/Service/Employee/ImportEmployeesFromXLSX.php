<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportEmployeesFromXLSX extends XLSXIterator
{
    public const COLUMN_UUID = 0;
    public const COLUMN_PARENT_EMPLOYEE_UUID = 1;
    public const COLUMN_COMPANY_UUID = 2;
    public const COLUMN_DEPARTMENT_UUID = 3;
    public const COLUMN_POSITION_UUID = 4;
    public const COLUMN_CONTACT_TYPE_UUID = 5;
    public const COLUMN_ROLE_UUID = 6;
    public const COLUMN_EXTERNAL_UUID = 7;
    public const COLUMN_FIRST_NAME = 8;
    public const COLUMN_LAST_NAME = 9;
    public const COLUMN_EMPLOYMENT_FROM = 10;
    public const COLUMN_PESEL = 11;
    public const COLUMN_ACTIVE = 12;
    public const COLUMN_PHONES = 13;
    public const COLUMN_STREET = 14;
    public const COLUMN_POSTCODE = 15;
    public const COLUMN_CITY = 16;
    public const COLUMN_COUNTRY = 17;

    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): ?string
    {
        [
            $uuid,
            $parentEmployeeUUID,
            $companyUUID,
            $departmentUUID,
            $positionUUID,
            $contactTypeUUID,
            $roleUUID,
            $externalUUID,
            $firstName,
            $lastName,
            $employmentFrom,
            $pesel,
            $active,
            $phones,
            $street,
            $postcode,
            $city,
            $country,
        ] = $row;

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

    private function validateRequiredField(string|bool|null $value): ?string
    {
        if (empty($value)) {
            return $this->formatErrorMessage('company.uuid.required');
        }

        return null;
    }

    private function validateEmployeeFirstAndLAstName(?string $firstName, $kind): ?string
    {
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
