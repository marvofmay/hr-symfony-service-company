<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\EmailValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportDepartmentsFromXLSX extends XLSXIterator
{
    public const int COLUMN_DEPARTMENT_NAME                 = 0;
    public const int COLUMN_DEPARTMENT_INTERNAL_CODE        = 1;
    public const int COLUMN_DEPARTMENT_DESCRIPTION          = 2;
    public const int COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE = 3;
    public const int COLUMN_COMPANY_UUID                    = 4;
    public const int COLUMN_ACTIVE                          = 5;
    public const int COLUMN_PHONE                           = 6;
    public const int COLUMN_EMAIL                           = 7;
    public const int COLUMN_WEBSITE                         = 8;
    public const int COLUMN_STREET                          = 9;
    public const int COLUMN_POSTCODE                        = 10;
    public const int COLUMN_CITY                            = 11;
    public const int COLUMN_COUNTRY                         = 12;

    private array $errorMessages = [];

    public function __construct(
        private readonly string                     $filePath,
        private readonly TranslatorInterface        $translator,
        private readonly CompanyReaderInterface     $companyReaderRepository,
        private readonly DepartmentReaderInterface  $departmentReaderRepository,
        private readonly DepartmentAggregateCreator $departmentAggregateCreator,
        private readonly DepartmentAggregateUpdater $departmentAggregateUpdater,
        private readonly ImportDepartmentsPreparer  $importDepartmentsPreparer,
        private readonly CacheInterface             $cache,
        private readonly UpdateImportAction         $updateImportAction,
        private readonly ImportLogMultipleCreator   $importLogMultipleCreator,
        private readonly MessageService             $messageService,
        private readonly MessageBusInterface        $eventBus,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $this->errorMessages = [];

        [
            $name,
            $internalCode,
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
            $this->validateDepartmentName((string)$name),
            $this->validateDepartmentInternalCode((string)$internalCode),
            $this->validateDepartmentDescription((string)$description),
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
            if (null !== $errorMessage) {
                $this->errorMessages[] = $errorMessage;
            }
        }

        return $this->errorMessages;
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

    private function validateDepartmentInternalCode(?string $internalCode): ?string
    {
        if (empty($internalCode)) {
            return $this->formatErrorMessage('department.internalCode.required');
        }

        return null;
    }

    private function validateDepartmentDescription(?string $description): ?string
    {
        if (empty($description)) {
            return null;
        }

        if (strlen($description) < 30) {
            return $this->formatErrorMessage('department.description.minimumLength', [':qty' => 30]);
        }

        return null;
    }

    private function validateParentDepartmentUUID(string|int|null $parentDepartmentInternalCode): ?string
    {
        if (empty($parentDepartmentInternalCode) || !is_string($parentDepartmentInternalCode)) {
            return null;
        }

        $cacheKey = 'import_department_internal_code_' . $parentDepartmentInternalCode;

        $exists = $this->cache->get($cacheKey, function () use ($parentDepartmentInternalCode) {
            return $this->departmentReaderRepository->isDepartmentExistsWithInternalCode($parentDepartmentInternalCode);
        });

        if (!$exists) {
            return $this->formatErrorMessage('department.internalCode.notExists', [':uuid' => $parentDepartmentInternalCode]);
        }

        return null;
    }

    private function validateCompanyUUID(?string $companyUUID): ?string
    {
        if (empty($companyUUID)) {
            return $this->formatErrorMessage('department.companyUUID.required');
        }

        $cacheKey = 'import_company_uuid_' . $companyUUID;

        $exists = $this->cache->get($cacheKey, function () use ($companyUUID) {
            return null !== $this->companyReaderRepository->getCompanyByUUID($companyUUID);
        });

        if (!$exists) {
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
        // $errorMessage = WebsiteValidator::validate($website);
        // if (null !== $errorMessage) {
        //    return $this->formatErrorMessage($errorMessage, [], 'validators');
        // }

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

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = 'departments'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            $this->rowIndex
        );
    }

    private function resolveParentUUID(array $row, array $internalCodeMap): ?DepartmentUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE] ?? null;
        if ($parentRaw === null) {
            return null;
        }

        $parentInternalCode = trim((string)$parentRaw);
        if ($parentInternalCode === '') {
            return null;
        }

        if (isset($internalCodeMap[$parentInternalCode])) {
            return $internalCodeMap[$parentInternalCode];
        }

        $existingParentDepartment = $this->departmentReaderRepository->getDepartmentByInternalCode($parentInternalCode);

        return DepartmentUUID::fromString($existingParentDepartment->getUUID()->toString());
    }

    public function run(Import $import): array
    {
        $errors = $this->validateBeforeImport();

        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('department.import.error', [], 'departments') . ': ' . $error)
                );
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);

            return $this->import();
        } else {
            [$preparedRows, $internalCodeMap] = $this->importDepartmentsPreparer->prepare($this->import());

            foreach ($preparedRows as $row) {
                $parentUUID = $this->resolveParentUUID($row, $internalCodeMap);

                $internalCode = trim((string)$row[self::COLUMN_DEPARTMENT_INTERNAL_CODE]);
                $uuid = $internalCodeMap[$internalCode];

                if (!$row['_is_department_already_exists_with_internal_code']) {
                    $this->departmentAggregateCreator->create($row, $uuid, $parentUUID);
                } else {
                    $this->departmentAggregateUpdater->update($row, $parentUUID);
                }
            }

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }

        return $preparedRows;
    }
}
