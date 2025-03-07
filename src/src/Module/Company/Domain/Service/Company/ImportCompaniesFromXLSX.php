<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportCompaniesFromXLSX extends XLSXIterator
{
    public function __construct(
        private readonly string $filePath,
        private readonly TranslatorInterface $translator,
        private readonly CompanyReaderInterface $companyReaderRepository,
    ) {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): ?string
    {
        [$fullName, $shortName, $parentUUID, $active] = $row + [null, null, null, true];

        if ($errorMessage = $this->validateCompanyFullName($fullName)) {
            return $errorMessage;
        }

        if ($this->companyExists($fullName)) {
            return $this->formatErrorMessage('company.fullName.alreadyExists');
        }

        //ToDo: add validation is exist ParentCompanyByUUID

        return null;
    }

    private function validateCompanyFullName(?string $fullName): ?string
    {
        if (empty($fullName)) {
            return $this->formatErrorMessage('role.name.required');
        }

        if (strlen($fullName) < 3) {
            return $this->formatErrorMessage('role.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function companyExists(string $companyFullName): bool
    {
        return $this->companyReaderRepository->isCompanyExists($companyFullName);
    }

    private function formatErrorMessage(string $translationKey, array $parameters = []): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, 'companies'),
            $this->translator->trans('row'),
            count($this->errors) + 2
        );
    }
}
