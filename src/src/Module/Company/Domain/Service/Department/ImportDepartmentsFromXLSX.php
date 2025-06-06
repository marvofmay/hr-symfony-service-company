<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportDepartmentsFromXLSX extends XLSXIterator
{
    public const int COLUMN_DEPARTMENT_UUID        = 0;
    public const int COLUMN_DEPARTMENT_NAME        = 1;
    public const int COLUMN_DEPARTMENT_DESCRIPTION = 2;
    public const int COLUMN_PARENT_DEPARTMENT_UUID = 3;
    public const int COLUMN_COMPANY_UUID           = 4;
    public const int COLUMN_ACTIVE                 = 5;
    public const int COLUMN_PHONE                  = 6;
    public const int COLUMN_EMAIL                  = 7;
    public const int COLUMN_WEBSITE                = 8;
    public const int COLUMN_STREET                 = 9;
    public const int COLUMN_POSTCODE               = 10;
    public const int COLUMN_CITY                   = 11;
    public const int COLUMN_COUNTRY                = 12;


    public function __construct(
        private readonly string                  $filePath,
        private readonly TranslatorInterface     $translator,
        private readonly CompanyReaderInterface  $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $errorMessages = [];
        [
            $departmentUUID,
            $name,
            $description,
            $parentDepartmentUUID,
            $companyUUID,
            $active,
            $phone,
            $email,
            $website,
            $street,
            $postcode,
            $city,
            $country,
        ] = $row + [null, null, null, null, null, false, null, null, null, null, null, null, null];

        if ($errorMessage = $this->validateDepartmentName($name)) {
            $errorMessages[] = $errorMessage;
        }

        if (is_string($departmentUUID)) {
            if ($this->isDepartmentExistsWithName($name, $departmentUUID)) {
                $errorMessages[] = $this->formatErrorMessage('department.name.alreadyExists', [], 'departments');
            }
        }

        if (is_int($departmentUUID)) {
            if ($this->isDepartmentExistsWithName($name)) {
                $errorMessages[] = $this->formatErrorMessage('department.name.alreadyExists', [], 'departments');
            }
        }

        if (is_string($parentDepartmentUUID) && !$this->isParentDepartmentExists($parentDepartmentUUID)) {
            $errorMessages[] = $this->formatErrorMessage('department.parent.notExists', [], 'departments');
        }

        if (empty($companyUUID)) {
            $errorMessages[] = $this->formatErrorMessage('department.companyUUID.required', [], 'departments');
        }

        if (is_string($companyUUID) && !$this->isCompanyExists($companyUUID)) {
            $errorMessages[] = $this->formatErrorMessage('industry.notExists', [], 'industries');
        }

        if ($errorMessage = $this->validateActive($active)) {
            $errorMessages[] = $this->formatErrorMessage($errorMessage, [], 'companies');
        }

        if (empty($street)) {
            $errorMessages[] = $this->formatErrorMessage('company.address.street.required', [], 'companies');
        }

        if (empty($postcode)) {
            $errorMessages[] = $this->formatErrorMessage('company.address.postcode.required', [], 'companies');
        }

        if (empty($city)) {
            $errorMessages[] = $this->formatErrorMessage('company.address.city.required', [], 'companies');
        }

        if (empty($country)) {
            $errorMessages[] = $this->formatErrorMessage('company.address.country.required', [], 'companies');
        }

        return $errorMessages;
    }

    private function validateDepartmentName(?string $name): ?string
    {
        if (empty($name)) {
            return $this->formatErrorMessage('department.name.required', [], 'departments');
        }

        if (strlen($name) < 3) {
            return $this->formatErrorMessage('department.name.minimumLength', [':qty' => 3], 'departments');
        }

        return null;
    }

    private function isDepartmentExistsWithName(string $name, ?string $departmentUUID = null): bool
    {
        return $this->departmentReaderRepository->isDepartmentExistsWithName($name, $departmentUUID);
    }

    private function validateActive(?int $active): ?string
    {
        if (null !== $active && !in_array($active, [0, 1])) {
            return $this->formatErrorMessage('department.active.invalid', [], 'companies');
        }

        return null;
    }

    private function isCompanyExists(string $companyUUID): bool
    {
        return $this->companyReaderRepository->isCompanyExistsWithUUID($companyUUID);
    }

    private function isParentDepartmentExists(string $parentDepartmentUUID): bool
    {
        return $this->departmentReaderRepository->isDepartmentExistsWithUUID($parentDepartmentUUID);
    }


    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = null): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            $this->rowIndex
        );
    }
}
