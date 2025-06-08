<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\EmailValidator;
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

    private array $errorMessages = [];

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
        $this->errorMessages = [];

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

        $validations = [
            $this->isDepartmentExists($name, is_string($departmentUUID) ? $departmentUUID : null),
            $this->validateDepartmentUUID($departmentUUID),
            $this->validateDepartmentName($name),
            $this->validateDepartmentDescription($description),
            $this->validateParentDepartmentUUID($parentDepartmentUUID),
            $this->validateCompanyUUID($companyUUID),
            $this->validateActive($active),
            $this->validatePhone($phone),
            $this->validateEmail((string)$email),
            $this->validateWebsite($website),
            $this->validateStreet($street),
            $this->validatePostcode($postcode),
            $this->validateCity($city),
            $this->validateCountry($country),
        ];

        foreach ($validations as $errorMessage) {
            if ($errorMessage !== null) {
                $this->errorMessages[] = $errorMessage;
            }
        }

        return $this->errorMessages;
    }

    private function validateDepartmentUUID(string|int|null $departmentUUID): ?string
    {
        if (empty($departmentUUID)) {
            return null;
        }

        if (is_string($departmentUUID)) {
            $department = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
            if (null === $department) {
                return $this->formatErrorMessage('department.uuid.notExists', [':uuid' => $departmentUUID]);
            }
        }

        return null;
    }

    private function validateDepartmentName(?string $name): ?string
    {
        if (empty($name)) {
            return $this->formatErrorMessage('department.name.required');
        }

        if (strlen($name) < 3) {
            return $this->formatErrorMessage('department.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function validateDepartmentDescription(?string $description): ?string
    {
        if (empty($description)) {
            return null;
        }

        if (strlen($description) < 50) {
            return $this->formatErrorMessage('department.description.minimumLength', [':qty' => 50]);
        }

        return null;
    }

    private function validateParentDepartmentUUID(string|int|null $parentDepartmentUUID): ?string
    {
        if (empty($parentDepartmentUUID)) {
            return null;
        }

        if (is_string($parentDepartmentUUID)) {
            $parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($parentDepartmentUUID);
            if (null === $parentDepartment) {
                return $this->formatErrorMessage('department.uuid.notExists', [':uuid' => $parentDepartmentUUID]);
            }
        }

        return null;
    }

    private function validateCompanyUUID(?string $companyUUID): ?string
    {
        if (empty($companyUUID)) {
            return $this->formatErrorMessage('department.companyUUID.required');
        }

        $company = $this->companyReaderRepository->getCompanyByUUID($companyUUID);
        if (null === $company) {
            return $this->formatErrorMessage('company.uuid.notExists', [':uuid' => $companyUUID], 'companies');
        }

        return null;
    }

    private function validateActive(?int $active): ?string
    {
        $errorMessage = BoolValidator::validate($active);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validatePhone(?string $phone): ?string
    {
        if (null === $phone) {
            return $this->formatErrorMessage('department.contact.phone.required');
        }

        return null;
    }

    private function validateEmail(?string $email): ?string
    {
        if (empty($email)) {
            return $this->formatErrorMessage('department.contact.email.required');
        }

        $errorMessage = EmailValidator::validate($email);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateWebsite(?string $website): ?string
    {
        //$errorMessage = WebsiteValidator::validate($website);
        //if (null !== $errorMessage) {
        //    return $this->formatErrorMessage($errorMessage, [], 'validators');
        //}

        return null;
    }

    private function validateStreet(?string $street): ?string
    {
        if (null === $street) {
            return $this->formatErrorMessage('department.address.street.required');
        }

        return null;
    }

    private function validatePostcode(?string $postcode): ?string
    {
        if (null === $postcode) {
            return $this->formatErrorMessage('department.address.postcode.required');
        }

        return null;
    }

    private function validateCity(?string $city): ?string
    {
        if (null === $city) {
            return $this->formatErrorMessage('department.address.city.required');
        }

        return null;
    }

    private function validateCountry(?string $country): ?string
    {
        if (null === $country) {
            return $this->formatErrorMessage('department.address.country.required');
        }

        return null;
    }

    private function isDepartmentExists(string $name, ?string $departmentUUID = null): ?string
    {
        $isDepartmentExists = $this->departmentReaderRepository->isDepartmentExistsWithName($name, $departmentUUID);
        if ($isDepartmentExists) {
            return $this->formatErrorMessage('department.alreadyExists', [':name' => $name]);
        }

        return null;
    }

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = 'departments'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            $this->rowIndex
        );
    }
}
