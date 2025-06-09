<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Enum\DateFormatEnum;
use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\DateFormatValidator;
use App\Common\Shared\Utils\EmailValidator;
use App\Common\Shared\Utils\PESELValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader\ContractTypeReaderRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportEmployeesFromXLSX extends XLSXIterator
{
    public const COLUMN_EMPLOYEE_UUID        = 0;
    public const COLUMN_DEPARTMENT_UUID      = 1;
    public const COLUMN_POSITION_UUID        = 2;
    public const COLUMN_CONTACT_TYPE_UUID    = 3;
    public const COLUMN_ROLE_UUID            = 4;
    public const COLUMN_PARENT_EMPLOYEE_UUID = 5;
    public const COLUMN_EXTERNAL_UUID        = 6;
    public const COLUMN_FIRST_NAME           = 7;
    public const COLUMN_LAST_NAME            = 8;
    public const COLUMN_EMPLOYMENT_FROM      = 9;
    public const COLUMN_EMPLOYMENT_TO        = 10;
    public const COLUMN_PESEL                = 11;
    public const COLUMN_ACTIVE               = 12;
    public const COLUMN_EMAIL                = 13;
    public const COLUMN_PHONE                = 14;
    public const COLUMN_STREET               = 15;
    public const COLUMN_POSTCODE             = 16;
    public const COLUMN_CITY                 = 17;
    public const COLUMN_COUNTRY              = 18;

    private array $errorMessages = [];

    public function __construct(
        private readonly string                       $filePath,
        private readonly TranslatorInterface          $translator,
        private readonly DepartmentReaderInterface    $departmentReaderRepository,
        private readonly EmployeeReaderInterface      $employeeReaderRepository,
        private readonly PositionReaderInterface      $positionReaderRepository,
        private readonly ContractTypeReaderRepository $contractTypeReaderRepository,
        private readonly RoleReaderInterface          $roleReaderRepository,
        private readonly CacheInterface               $cache,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $this->errorMessages = [];

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

        $validations = [
            $this->validateEmployeeExists((string)$pesel, (string)$email, is_string($employeeUUID) ? $employeeUUID : null),
            $this->validateEmployeeUUID($employeeUUID),
            $this->validateDepartmentUUID($departmentUUID),
            $this->validatePositionUUID($positionUUID),
            $this->validateContractTypeUUID($contactTypeUUID),
            $this->validateRoleUUID($roleUUID),
            $this->validateParentEmployeeUUID($parentEmployeeUUID),
            $this->validateExternalUUID($externalUUID),
            $this->validateFirstName($firstName),
            $this->validateLastName($lastName),
            $this->validateEmploymentFrom((string)$employmentFrom),
            $this->validateEmploymentTo((string)$employmentTo),
            $this->validatePESEL((string)$pesel),
            $this->validateActive($active),
            $this->validateEmail((string)$email),
            $this->validatePhone($phone),
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

    private function validateEmployeeExists(string $pesel, string $email, ?string $employeeUUID): ?string
    {
        $isEmployeeExists = $this->employeeReaderRepository->isEmployeeExists($pesel, $email, $employeeUUID);
        if ($isEmployeeExists) {
            return $this->formatErrorMessage('employee.alreadyExists', [':pesel' => $pesel, ':email' => $email]);
        }

        return null;
    }

    private function validateEmployeeUUID(string|int|null $employeeUUID): ?string
    {
        if (empty($employeeUUID)) {
            return null;
        }

        if (is_string($employeeUUID)) {
            $employee = $this->employeeReaderRepository->getEmployeeByUUID($employeeUUID);
            if (null === $employee) {
                return $this->formatErrorMessage('employee.uuid.notExists', [':uuid' => $employeeUUID]);
            }
        }

        return null;
    }

    private function validateDepartmentUUID(?string $departmentUUID): ?string
    {
        if (empty($departmentUUID)) {
            return $this->formatErrorMessage('employee.departmentUUID.required');
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
            return $this->formatErrorMessage('employee.positionUUID.required');
        }

        $position = $this->positionReaderRepository->getPositionByUUID($positionUUID);
        if (null === $position) {
            return $this->formatErrorMessage('position.uuid.notExists', [':uuid' => $positionUUID], 'positions');
        }

        return null;
    }

    private function validateContractTypeUUID(?string $contractTypeUUID): ?string
    {
        if (empty($contractTypeUUID)) {
            return $this->formatErrorMessage('employee.contractTypeUUID.required');
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
            return $this->formatErrorMessage('employee.roleUUID.required');
        }

        $cacheKey = 'import_employee_role_' . $roleUUID;

        $roleExists = $this->cache->get($cacheKey, function () use ($roleUUID) {
            return $this->roleReaderRepository->getRoleByUUID($roleUUID) !== null;
        });

        if (!$roleExists) {
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
                return $this->formatErrorMessage('employee.uuid.notExists', [':uuid' => $parentEmployeeUUID]);
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
        if (empty($employmentFrom)) {
            return $this->formatErrorMessage('employee.employmentFrom.required');
        }

        $errorMessage = DateFormatValidator::validate($employmentFrom, DateFormatEnum::DD_MM_YYYY->value);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage('employee.employmentFrom.' . $errorMessage, [':dateFormat' => DateFormatEnum::DD_MM_YYYY->value]);
        }

        return null;
    }

    private function validateEmploymentTo(?string $employmentTo): ?string
    {
        if (!empty($employmentTo)) {
            $errorMessage = DateFormatValidator::validate($employmentTo, DateFormatEnum::DD_MM_YYYY->value);
            if (null !== $errorMessage) {
                return $this->formatErrorMessage('employee.employmentTo.' . $errorMessage, [':dateFormat' => DateFormatEnum::DD_MM_YYYY->value]);
            }
        }

        return null;
    }

    private function validatePESEL(?string $pesel): ?string
    {
        if (null === $pesel) {
            return $this->formatErrorMessage('employee.pesel.required', [], 'employees');
        }

        $errorMessage = PESELValidator::validate($pesel);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
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

    private function validateEmail(?string $email): ?string
    {
        if (null === $email) {
            return $this->formatErrorMessage('employee.email.required', [], 'employees');
        }

        $errorMessage = EmailValidator::validate($email);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validatePhone(?string $phone): ?string
    {
        if (null === $phone) {
            return $this->formatErrorMessage('employee.phone.required', [], 'employees');
        }

        return null;
    }

    private function validateStreet(?string $street): ?string
    {
        if (null === $street) {
            return $this->formatErrorMessage('employee.street.required', [], 'employees');
        }

        return null;
    }

    private function validatePostcode(?string $postcode): ?string
    {
        if (null === $postcode) {
            return $this->formatErrorMessage('employee.postcode.required', [], 'employees');
        }

        return null;
    }

    private function validateCity(?string $city): ?string
    {
        if (null === $city) {
            return $this->formatErrorMessage('employee.city.required', [], 'employees');
        }

        return null;
    }

    private function validateCountry(?string $country): ?string
    {
        if (null === $country) {
            return $this->formatErrorMessage('employee.country.required', [], 'employees');
        }

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

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = 'employees'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
