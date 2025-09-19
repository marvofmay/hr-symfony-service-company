<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Enum\DateFormatEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\DateFormatValidator;
use App\Common\Shared\Utils\EmailValidator;
use App\Common\Shared\Utils\PESELValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportEmployeesFromXLSX extends XLSXIterator
{
    public const int COLUMN_FIRST_NAME            = 0;
    public const int COLUMN_LAST_NAME             = 1;
    public const int COLUMN_PESEL                 = 2;
    public const int COLUMN_EMAIL                 = 3;
    public const int COLUMN_PHONE                 = 4;
    public const int COLUMN_STREET                = 5;
    public const int COLUMN_POSTCODE              = 6;
    public const int COLUMN_CITY                  = 7;
    public const int COLUMN_COUNTRY               = 8;
    public const int COLUMN_EMPLOYMENT_FROM       = 9;
    public const int COLUMN_DEPARTMENT_UUID       = 10;
    public const int COLUMN_POSITION_UUID         = 11;
    public const int COLUMN_CONTACT_TYPE_UUID     = 12;
    public const int COLUMN_ROLE_UUID             = 13;
    public const int COLUMN_PARENT_EMPLOYEE_PESEL = 14;
    public const int COLUMN_INTERNAL_CODE         = 15;
    public const int COLUMN_EXTERNAL_UUID         = 16;
    public const int COLUMN_EMPLOYMENT_TO         = 17;
    public const int COLUMN_ACTIVE                = 18;

    public const string COLUMN_DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS = '_is_employee_already_exists_with_pesel';
    public const string COLUMN_DYNAMIC_AGGREGATE_UUID                        = '_aggregate_uuid';


    private array $errorMessages = [];

    public function __construct(
        private readonly string                      $filePath,
        private readonly TranslatorInterface         $translator,
        private readonly DepartmentReaderInterface   $departmentReaderRepository,
        private readonly EmployeeReaderInterface     $employeeReaderRepository,
        private readonly PositionReaderInterface     $positionReaderRepository,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly RoleReaderInterface         $roleReaderRepository,
        private readonly EmployeeAggregateCreator    $employeeAggregateCreator,
        private readonly EmployeeAggregateUpdater    $employeeAggregateUpdater,
        private readonly ImportEmployeesPreparer     $importEmployeesPreparer,
        private readonly CacheInterface              $cache,
        private readonly UpdateImportAction          $updateImportAction,
        private readonly ImportLogMultipleCreator    $importLogMultipleCreator,
        private readonly MessageService              $messageService,
        private readonly MessageBusInterface         $eventBus,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $this->errorMessages = [];
        [
            $firstName,
            $lastName,
            $pesel,
            $email,
            $phone,
            $street,
            $postcode,
            $city,
            $country,
            $employmentFrom,
            $departmentUUID,
            $positionUUID,
            $contactTypeUUID,
            $roleUUID,
            $parentEmployeePESEL,
            $internalCode,
            $externalUUID,
            $employmentTo,
            $active,
        ] = $row + [null, null, null, null, null, null, null, null, null, null, null, null, false, null, null, null, null, null, null];

        $validations = [
            $this->validateDepartmentUUID($departmentUUID),
            $this->validatePositionUUID($positionUUID),
            $this->validateContractTypeUUID($contactTypeUUID),
            $this->validateRoleUUID($roleUUID),
            $this->validateParentEmployeePESEL($parentEmployeePESEL),
            $this->validateExternalUUID($externalUUID),
            $this->validateInternalCode($internalCode),
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
            if (null !== $errorMessage) {
                $this->errorMessages[] = $errorMessage;
            }
        }

        return $this->errorMessages;
    }

    private function validateDepartmentUUID(?string $departmentUUID): ?string
    {
        if (empty($departmentUUID)) {
            return $this->formatErrorMessage('employee.departmentUUID.required');
        }

        $cacheKey = 'import_department_uuid_' . $departmentUUID;

        $departmentExists = $this->cache->get($cacheKey, function () use ($departmentUUID) {
            return $this->departmentReaderRepository->isDepartmentExistsWithUUID($departmentUUID);
        });

        if (!$departmentExists) {
            return $this->formatErrorMessage('department.uuid.notExists', [':uuid' => $departmentUUID], 'departments');
        }

        return null;
    }

    private function validatePositionUUID(?string $positionUUID): ?string
    {
        if (empty($positionUUID)) {
            return $this->formatErrorMessage('employee.positionUUID.required');
        }

        $cacheKey = 'import_position_uuid_' . $positionUUID;

        $positionExists = $this->cache->get($cacheKey, function () use ($positionUUID) {
            return $this->positionReaderRepository->isPositionWithUUIDExists($positionUUID);
        });

        if (!$positionExists) {
            return $this->formatErrorMessage('position.uuid.notExists', [':uuid' => $positionUUID], 'positions');
        }

        return null;
    }

    private function validateContractTypeUUID(?string $contractTypeUUID): ?string
    {
        if (empty($contractTypeUUID)) {
            return $this->formatErrorMessage('employee.contractTypeUUID.required');
        }

        $cacheKey = 'import_contract_type_uuid_' . $contractTypeUUID;

        $contractTypeExists = $this->cache->get($cacheKey, function () use ($contractTypeUUID) {
            return $this->contractTypeReaderRepository->isContractTypeWithUUIDExists($contractTypeUUID);
        });

        if (!$contractTypeExists) {
            return $this->formatErrorMessage('contractType.uuid.notExists', [':uuid' => $contractTypeUUID], 'contract_types');
        }

        return null;
    }

    private function validateRoleUUID(?string $roleUUID): ?string
    {
        if (empty($roleUUID)) {
            return $this->formatErrorMessage('employee.roleUUID.required');
        }

        $cacheKey = 'import_role_uuid_' . $roleUUID;

        $roleExists = $this->cache->get($cacheKey, function () use ($roleUUID) {
            return $this->roleReaderRepository->isRoleWithUUIDExists($roleUUID);
        });

        if (!$roleExists) {
            return $this->formatErrorMessage('role.uuid.notExists', [':uuid' => $roleUUID], 'roles');
        }

        return null;
    }

    private function validateParentEmployeePESEL(?string $parentEmployeePESEL): ?string
    {
        if (empty($parentEmployeePESEL)) {
            return null;
        }

        $errorMessage = PESELValidator::validate($parentEmployeePESEL);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateExternalUUID(?string $externalUUID): ?string
    {
        return null;
    }

    private function validateInternalCode(?string $internalCode): ?string
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

        $errorMessage = DateFormatValidator::validate($employmentFrom, DateFormatEnum::YYYY_MM_DD->value);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage('employee.employmentFrom.' . $errorMessage, [':dateFormat' => DateFormatEnum::YYYY_MM_DD->value]);
        }

        return null;
    }

    private function validateEmploymentTo(?string $employmentTo): ?string
    {
        if (!empty($employmentTo)) {
            $errorMessage = DateFormatValidator::validate($employmentTo, DateFormatEnum::YYYY_MM_DD->value);
            if (null !== $errorMessage) {
                return $this->formatErrorMessage('employee.employmentTo.' . $errorMessage, [':dateFormat' => DateFormatEnum::YYYY_MM_DD->value]);
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
            return $this->formatErrorMessage('employee.email.required');
        }

        // ToDo:: check if employee with an email and a different PESEL alreday exists in the DB

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


    private function resolveParentUUID(array $row, array $peselMap): ?EmployeeUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_EMPLOYEE_PESEL] ?? null;
        if ($parentRaw === null) {
            return null;
        }

        $parentPESEL = trim((string)$parentRaw);
        if ($parentPESEL === '') {
            return null;
        }

        if (isset($peselMap[$parentPESEL])) {
            return $peselMap[$parentPESEL];
        }

        $existingParentEmployee = $this->employeeReaderRepository->getEmployeeByPESEL($parentPESEL);

        return EmployeeUUID::fromString($existingParentEmployee->getUUID()->toString());
    }

    public function run(Import $import): array
    {
        $errors = $this->validateBeforeImport();

        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('employee.import.error', [], 'employees') . ': ' . $error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $peselMap] = $this->importEmployeesPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $peselMap);

                $pesel = trim((string)$row[self::COLUMN_PESEL]);
                $uuid = $peselMap[$pesel];

                if (!$row[ImportEmployeesFromXLSX::COLUMN_DYNAMIC_IS_EMPLOYEE_WITH_PESEL_ALREADY_EXISTS]) {
                    $this->employeeAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->employeeAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }

        return $preparedRows;
    }
}
