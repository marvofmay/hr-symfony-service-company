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
    public const int    COLUMN_DEPARTMENT_NAME                                         = 0;
    public const int    COLUMN_DEPARTMENT_INTERNAL_CODE                                = 1;
    public const int    COLUMN_STREET                                                  = 2;
    public const int    COLUMN_POSTCODE                                                = 3;
    public const int    COLUMN_CITY                                                    = 4;
    public const int    COLUMN_COUNTRY                                                 = 5;
    public const int    COLUMN_PHONE                                                   = 6;
    public const int    COLUMN_COMPANY_UUID                                            = 7;
    public const int    COLUMN_DEPARTMENT_DESCRIPTION                                  = 8;
    public const int    COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE                         = 9;
    public const int    COLUMN_EMAIL                                                   = 10;
    public const int    COLUMN_WEBSITE                                                 = 11;
    public const int    COLUMN_ACTIVE                                                  = 12;
    public const string COLUMN_DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS = '_is_department_already_exists_with_internal_code';
    public const string COLUMN_DYNAMIC_AGGREGATE_UUID                                  = '_aggregate_uuid';

    private array $errorMessages = [];

    private array $validators;

    public function __construct(
        private readonly string                           $filePath,
        private readonly TranslatorInterface              $translator,
        private readonly CompanyReaderInterface           $companyReaderRepository,
        private readonly DepartmentReaderInterface        $departmentReaderRepository,
        private readonly DepartmentAggregateCreator       $departmentAggregateCreator,
        private readonly DepartmentAggregateUpdater       $departmentAggregateUpdater,
        private readonly ImportDepartmentsPreparer        $importDepartmentsPreparer,
        private readonly CacheInterface                   $cache,
        private readonly UpdateImportAction               $updateImportAction,
        private readonly ImportLogMultipleCreator         $importLogMultipleCreator,
        private readonly MessageService                   $messageService,
        private readonly MessageBusInterface              $eventBus,
        private readonly ImportDepartmentsReferenceLoader $importDepartmentsReferenceLoader,
        private readonly iterable                         $sharedValidators,
        private readonly iterable                         $departmentsValidators,
    )
    {
        parent::__construct($this->filePath, $this->translator);

        $this->validators = array_merge(
            iterator_to_array($this->sharedValidators),
            iterator_to_array($this->departmentsValidators),
        );
    }

    public function validateRow(array $row, int $index): array
    {
        $this->importDepartmentsReferenceLoader->preload($this->import());
        $companies = $this->importDepartmentsReferenceLoader->getCompanies();
        $departments = $this->importDepartmentsReferenceLoader->getDepartments();

        $this->errorMessages = [];
        foreach ($this->validators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'companies'   => $companies,
                    'departments' => $departments,
                ]
            );
            if (null !== $error) {
                $this->errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $this->errorMessages;
    }

    //private function validateDepartmentName(?string $name): ?string
    //{
    //    if (empty($name)) {
    //        return $this->formatErrorMessage('department.name.required');
    //    }
    //
    //    if (strlen($name) < 3) {
    //        return $this->formatErrorMessage('department.name.minimumLength', [':qty' => 3]);
    //    }
    //
    //    return null;
    //}
    //
    //private function validateDepartmentInternalCode(?string $internalCode): ?string
    //{
    //    if (empty($internalCode)) {
    //        return $this->formatErrorMessage('department.internalCode.required');
    //    }
    //
    //    return null;
    //}
    //
    //private function validateDepartmentDescription(?string $description): ?string
    //{
    //    if (empty($description)) {
    //        return null;
    //    }
    //
    //    if (strlen($description) < 30) {
    //        return $this->formatErrorMessage('department.description.minimumLength', [':qty' => 30]);
    //    }
    //
    //    return null;
    //}
    //
    //private function validateParentDepartmentInternalCode(?string $parentDepartmentInternalCode): ?string
    //{
    //    return null;
    //}
    //
    //private function validateCompanyUUID(?string $companyUUID): ?string
    //{
    //    if (empty($companyUUID)) {
    //        return $this->formatErrorMessage('department.companyUUID.required');
    //    }
    //
    //    $cacheKey = 'import_company_uuid_' . $companyUUID;
    //
    //    $exists = $this->cache->get($cacheKey, function () use ($companyUUID) {
    //        return $this->companyReaderRepository->isCompanyExistsWithUUID($companyUUID);
    //    });
    //
    //    if (!$exists) {
    //        return $this->formatErrorMessage('company.uuid.notExists', [':uuid' => $companyUUID], 'companies');
    //    }
    //
    //    return null;
    //}
    //
    //private function validateActive(?int $active): ?string
    //{
    //    $errorMessage = BoolValidator::validate($active);
    //    if (null !== $errorMessage) {
    //        return $this->formatErrorMessage($errorMessage, [], 'validators');
    //    }
    //
    //    return null;
    //}
    //
    //private function validatePhone(?string $phone): ?string
    //{
    //    if (null === $phone) {
    //        return $this->formatErrorMessage('department.contact.phone.required');
    //    }
    //
    //    return null;
    //}
    //
    //private function validateEmail(?string $email): ?string
    //{
    //    // if (empty($email)) {
    //    //    return $this->formatErrorMessage('department.contact.email.required');
    //    // }
    //
    //    if (empty($email)) {
    //        return null;
    //    }
    //
    //    $errorMessage = EmailValidator::validate($email);
    //    if (null !== $errorMessage) {
    //        return $this->formatErrorMessage($errorMessage, [], 'validators');
    //    }
    //
    //    return null;
    //}
    //
    //private function validateWebsite(?string $website): ?string
    //{
    //    // $errorMessage = WebsiteValidator::validate($website);
    //    // if (null !== $errorMessage) {
    //    //    return $this->formatErrorMessage($errorMessage, [], 'validators');
    //    // }
    //
    //    return null;
    //}

    private function resolveParentUUID(array $row, array $internalCodeMap): ?DepartmentUUID
    {
        $parentRaw = $row[self::COLUMN_PARENT_DEPARTMENT_INTERNAL_CODE] ?? null;
        if (null === $parentRaw) {
            return null;
        }

        $parentInternalCode = trim((string)$parentRaw);
        if ('' === $parentInternalCode) {
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

                if (!$row[ImportDepartmentsFromXLSX::COLUMN_DYNAMIC_IS_DEPARTMENT_WITH_INTERNAL_CODE_ALREADY_EXISTS]) {
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
