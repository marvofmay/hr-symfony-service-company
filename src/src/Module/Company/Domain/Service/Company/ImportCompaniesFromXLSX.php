<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Shared\Utils\NIPValidator;
use App\Common\Shared\Utils\REGONValidator;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportCompaniesFromXLSX extends XLSXIterator
{
    public const int COLUMN_COMPANY_UUID        = 0;
    public const int COLUMN_COMPANY_FULL_NAME   = 1;
    public const int COLUMN_COMPANY_SHORT_NAME  = 2;
    public const int COLUMN_COMPANY_DESCRIPTION = 3;
    public const int COLUMN_PARENT_COMPANY_UUID = 4;
    public const int COLUMN_INDUSTRY_UUID       = 5;
    public const int COLUMN_NIP                 = 6;
    public const int COLUMN_REGON               = 7;
    public const int COLUMN_ACTIVE              = 8;


    public function __construct(
        private readonly string                 $filePath,
        private readonly TranslatorInterface    $translator,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly IndustryReaderInterface $industryReaderRepository,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $errorMessages = [];
        [
            $companyUUID,
            $fullName,
            $shortName,
            $description,
            $parentUUID,
            $industryUUID,
            $nip,
            $regon,
            $active,
        ] = $row + [null, null, null, null, null, null, null, null, true,];

        if ($errorMessage = $this->validateCompanyFullName($fullName)) {
            $errorMessages[] = $errorMessage;
        }

        if ($this->isCompanyExists($fullName)) {
            $errorMessages[] = $this->formatErrorMessage('company.fullName.alreadyExists', [], 'companies');
        }

        if (!$this->isParentCompanyExists($parentUUID)) {
            $errorMessages[] = $this->formatErrorMessage('company.parent.notExists', [], 'companies');
        }

        if ($errorMessage = $this->validateNIP((string)$nip)) {
            $errorMessages[] = $this->formatErrorMessage($errorMessage, [], 'companies');
        }

        if ($errorMessage = $this->validateREGON((string)$regon)) {
            $errorMessages[] = $this->formatErrorMessage($errorMessage, [], 'companies');
        }

        if (empty($industryUUID)) {
            $errorMessages[] = $this->formatErrorMessage('company.industryUUID.required', [], 'companies');
        }

        if (is_string($industryUUID) && !$this->isIndustryExists($industryUUID)) {
            $errorMessages[] = $this->formatErrorMessage('industry.notExists', [], 'industries');
        }

        return $errorMessages;
    }

    private function validateCompanyFullName(?string $fullName): ?string
    {
        if (empty($fullName)) {
            return $this->formatErrorMessage('company.fullName.required', [], 'companies');
        }

        if (strlen($fullName) < 3) {
            return $this->formatErrorMessage('company.name.minimumLength', [':qty' => 3], 'companies');
        }

        return null;
    }

    private function isCompanyExists(string $companyFullName): bool
    {
        return $this->companyReaderRepository->isCompanyExists($companyFullName);
    }

    private function isParentCompanyExists(string $parentCompanyUUID): bool
    {
        return $this->companyReaderRepository->isCompanyWithUUIDExists($parentCompanyUUID);
    }

    private function isIndustryExists(string $industryUUID): bool
    {
        return $this->industryReaderRepository->isIndustryWithUUIDExists($industryUUID);
    }

    private function validateNIP(?string $nip): ?string
    {
        $nip = preg_replace('/\D/', '', $nip ?? '');
        if ($nip === '') {
            return 'company.nip.required';
        }

        return NIPValidator::validate($nip);
    }

    private function validateREGON(?string $regon): ?string
    {
        $regon = preg_replace('/\D/', '', $regon ?? '');
        if ($regon === '') {
            return 'company.regon.required';
        }

        return REGONValidator::validate($regon);
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
