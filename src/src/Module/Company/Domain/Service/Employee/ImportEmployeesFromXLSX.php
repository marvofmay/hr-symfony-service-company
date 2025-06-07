<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Shared\Utils\PESELValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader\ContractTypeReaderRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportEmployeesFromXLSX extends XLSXIterator
{
    public const COLUMN_UUID                 = 0;
    public const COLUMN_PARENT_EMPLOYEE_UUID = 1;
    public const COLUMN_DEPARTMENT_UUID      = 2;
    public const COLUMN_POSITION_UUID        = 3;
    public const COLUMN_CONTACT_TYPE_UUID    = 4;
    public const COLUMN_ROLE_UUID            = 5;
    public const COLUMN_EXTERNAL_UUID        = 6;
    public const COLUMN_FIRST_NAME           = 7;
    public const COLUMN_LAST_NAME            = 8;
    public const COLUMN_EMPLOYMENT_FROM      = 9;
    public const COLUMN_PESEL                = 10;
    public const COLUMN_ACTIVE               = 11;
    public const COLUMN_PHONE                = 12;
    public const COLUMN_STREET               = 13;
    public const COLUMN_POSTCODE             = 14;
    public const COLUMN_CITY                 = 15;
    public const COLUMN_COUNTRY              = 16;

    private array $errorMessages = [];

    public function __construct(
        private readonly string                       $filePath,
        private readonly TranslatorInterface          $translator,
        private readonly DepartmentReaderInterface    $departmentReaderRepository,
        private readonly EmployeeReaderInterface      $employeeReaderRepository,
        private readonly PositionReaderInterface      $positionReaderRepository,
        private readonly ContractTypeReaderRepository $contractTypeReaderRepository,
        private readonly RoleReaderInterface          $roleReaderRepository,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        [
            $employeeUUID,
            $departmentUUID,
            $positionUUID,
            $contactTypeUUID,
            $roleUUID,
            $parentEmployeeUUID,
            $externalUUID,
            $firstName,
            $lastName,
            $employmentFrom,
            $employmentTo,
            $pesel,
            $active,
            $email,
            $phone,
            $street,
            $postcode,
            $city,
            $country,
        ] = $row + [null, null, null, null, null, null, null, null, null, null, null, null, false, null, null, null, null, null, null];

        $this->errorMessages[] = $this->validateEmployeeUUID($employeeUUID);
        $this->errorMessages[] = $this->validateDepartmentUUID($departmentUUID);
        $this->errorMessages[] = $this->validatePositionUUID($positionUUID);
        $this->errorMessages[] = $this->validateContractTypeUUID($contactTypeUUID);
        $this->errorMessages[] = $this->validateRoleUUID($roleUUID);
        $this->errorMessages[] = $this->validateParentEmployeeUUID($parentEmployeeUUID);
        $this->errorMessages[] = $this->validateExternalUUID($externalUUID);
        $this->errorMessages[] = $this->validateFirstName($firstName);
        $this->errorMessages[] = $this->validateLastName($lastName);
        $this->errorMessages[] = $this->validateEmploymentFrom($employmentFrom);
        $this->errorMessages[] = $this->validateEmploymentTo($employmentTo);
        $this->errorMessages[] = $this->validatePESEL((string)$pesel);
        $this->errorMessages[] = $this->validateActive($active);
        $this->errorMessages[] = $this->validateEmail($email);
        $this->errorMessages[] = $this->validatePhone($phone);
        $this->errorMessages[] = $this->validateStreet($street);
        $this->errorMessages[] = $this->validatePostcode($postcode);
        $this->errorMessages[] = $this->validateCity($city);
        $this->errorMessages[] = $this->validateCountry($country);

        return $this->errorMessages;
    }

    private function validateEmployeeUUID(string|int|null $employeeUUID): ?string
    {
        if (empty($employeeUUID)) {
            return $this->formatErrorMessage('employee.uuid.required', [], 'employees');
        }

        if (is_string($employeeUUID)) {
            $employee = $this->employeeReaderRepository->getEmployeeByUUID($employeeUUID);
            if (null === $employee) {
                return $this->formatErrorMessage('employee.uuid.notExists', [':uuid' => $employeeUUID], 'employees');
            }
        }

        return null;
    }

    private function validateDepartmentUUID(?string $departmentUUID): ?string
    {
        if (empty($departmentUUID)) {
            return $this->formatErrorMessage('department.uuid.required', [], 'departments');
        }

        $department = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
        if (null === $department) {
            return $this->formatErrorMessage('department.uuid.notExists', [':uuid' => $departmentUUID], 'departments');
        }

        return null;
    }

    private function validatePositionUUID(?string $positionUUID): ?string
    {
        if (empty($positionUUID)) {
            return $this->formatErrorMessage('position.uuid.required', [], 'positions');
        }

        $position = $this->positionReaderRepository->getPositionByUUID($positionUUID);
        if (null === $position) {
            return $this->formatErrorMessage('positions.uuid.notExists', [':uuid' => $positionUUID], 'positions');
        }

        return null;
    }

    private function validateContractTypeUUID(?string $contractTypeUUID): ?string
    {
        if (empty($contractTypeUUID)) {
            return $this->formatErrorMessage('contractType.uuid.required', [], 'contract_types');
        }

        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($contractTypeUUID);
        if (null === $contractType) {
            return $this->formatErrorMessage('contractType.uuid.notExists', [':uuid' => $contractTypeUUID], 'contract_types');
        }

        return null;
    }

    private function validateRoleUUID(?string $roleUUID): ?string
    {
        if (empty($roleUUID)) {
            return $this->formatErrorMessage('role.uuid.required', [], 'roles');
        }

        $role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
        if (null === $role) {
            return $this->formatErrorMessage('role.uuid.notExists', [':uuid' => $roleUUID], 'roles');
        }

        return null;
    }

    private function validateParentEmployeeUUID(string|int|null $parentEmployeeUUID): ?string
    {
        if (empty($parentEmployeeUUID)) {
            return null;
        }

        if (is_string($parentEmployeeUUID)) {
            $employee = $this->employeeReaderRepository->getEmployeeByUUID($parentEmployeeUUID);
            if (null === $employee) {
                return $this->formatErrorMessage('employee.uuid.notExists', [':uuid' => $parentEmployeeUUID], 'employees');
            }
        }

        return null;
    }

    private function validateExternalUUID(?string $externalUUID): ?string
    {
        return null;
    }

    private function validateFirstName(?string $firstName): ?string
    {
        return $this->validateEmployeeFirstAndLAstName($firstName, 'firstName');
    }

    private function validateLastName(?string $lastName): ?string
    {
        return $this->validateEmployeeFirstAndLAstName($lastName, 'lastName');
    }

    private function validateEmploymentFrom(?string $employmentFrom): ?string
    {
        return null;
    }

    private function validateEmploymentTo(?string $employmentTo): ?string
    {
        return null;
    }

    private function validatePESEL(?string $pesel): ?string
    {
        if (null === $pesel) {
            return $this->formatErrorMessage('employee.pesel.required', [], 'employees');
        }

        $errorMessage = PESELValidator::validate($pesel);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'employees');
        }

        return null;
    }

    private function validateActive(?int $active): ?string
    {
        return null;
    }

    private function validateEmail(?string $email): ?string
    {
        return null;
    }

    private function validatePhone(?string $phone): ?string
    {
        return null;
    }

    private function validateStreet(?string $street): ?string
    {
        return null;
    }

    private function validatePostcode(?string $postcode): ?string
    {
        return null;
    }

    private function validateCity(?string $city): ?string
    {
        return null;
    }

    private function validateCountry(?string $country): ?string
    {
        return null;
    }

    private function validateEmployeeFirstAndLAstName(?string $name, $kind): ?string
    {
        if (empty($name)) {
            return $this->formatErrorMessage(sprintf('employee.%s.required', $kind));
        }

        if (strlen($name) < 3) {
            return $this->formatErrorMessage(sprintf('employee.%s.minimumLength', $kind), [':qty' => 3]);
        }

        return null;
    }

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = null): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
