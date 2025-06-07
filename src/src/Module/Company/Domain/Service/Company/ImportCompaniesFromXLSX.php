<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Shared\Utils\BoolValidator;
use App\Common\Shared\Utils\EmailValidator;
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
    public const int COLUMN_PHONE               = 9;
    public const int COLUMN_EMAIL               = 10;
    public const int COLUMN_WEBSITE             = 11;
    public const int COLUMN_STREET              = 12;
    public const int COLUMN_POSTCODE            = 13;
    public const int COLUMN_CITY                = 14;
    public const int COLUMN_COUNTRY             = 15;

    private array $errorMessages = [];

    public function __construct(
        private readonly string                  $filePath,
        private readonly TranslatorInterface     $translator,
        private readonly CompanyReaderInterface  $companyReaderRepository,
        private readonly IndustryReaderInterface $industryReaderRepository,
    )
    {
        parent::__construct($this->filePath, $this->translator);
    }

    public function validateRow(array $row): array
    {
        $this->errorMessages = [];

        [
            $companyUUID,
            $fullName,
            $shortName,
            $description,
            $parentCompanyUUID,
            $industryUUID,
            $nip,
            $regon,
            $active,
            $phone,
            $email,
            $website,
            $street,
            $postcode,
            $city,
            $country,
        ] = $row + [null, null, null, null, null, null, null, null, false, null, null, null, null, null, null, null];

        $validations = [
            $this->isCompanyExists((string)$nip, (string)$regon, is_string($companyUUID) ? $companyUUID : null),
            $this->validateCompanyUUID($companyUUID),
            $this->validateCompanyFullName($fullName),
            $this->validateCompanyShortName($shortName),
            $this->validateCompanyDescription($description),
            $this->validateParentCompanyUUID($parentCompanyUUID),
            $this->validateIndustryUUID($industryUUID),
            $this->validateNIP((string)$nip),
            $this->validateREGON((string)$regon),
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

    private function isCompanyExists($nip, $regon, $companyUUID): ?string
    {
        $isCompanyExists = $this->companyReaderRepository->isCompanyExists($nip, $regon, $companyUUID);
        if ($isCompanyExists) {
            return $this->formatErrorMessage('company.alreadyExists', [':nip' => $nip, ':regon' => $regon]);
        }

        return null;
    }

    private function validateCompanyUUID(string|int|null $companyUUID): ?string
    {
        if (empty($companyUUID)) {
            return null;
        }

        if (is_string($companyUUID)) {
            $company = $this->companyReaderRepository->getCompanyByUUID($companyUUID);
            if (null === $company) {
                return $this->formatErrorMessage('company.uuid.notExists', [':uuid' => $companyUUID]);
            }
        }

        return null;
    }

    private function validateCompanyFullName(?string $fullName): ?string
    {
        if (empty($fullName)) {
            return $this->formatErrorMessage('company.fullName.required');
        }

        if (strlen($fullName) < 3) {
            return $this->formatErrorMessage('company.name.minimumLength', [':qty' => 3]);
        }

        return null;
    }

    private function validateCompanyShortName(?string $shortName): ?string
    {
        return null;
    }

    private function validateCompanyDescription(?string $description): ?string
    {
        return null;
    }

    private function validateParentCompanyUUID(string|int|null $parentCompanyUUID): ?string
    {
        if (empty($parentCompanyUUID)) {
            return null;
        }

        if (is_string($parentCompanyUUID)) {
            $parentCompany = $this->companyReaderRepository->getCompanyByUUID($parentCompanyUUID);
            if (null === $parentCompany) {
                return $this->formatErrorMessage('company.uuid.notExists', [':uuid' => $parentCompanyUUID]);
            }
        }

        return null;
    }

    private function validateIndustryUUID(?string $industryUUID): ?string
    {
        if (empty($industryUUID)) {
            return $this->formatErrorMessage('company.industryUUID.required');
        }

        $industry = $this->industryReaderRepository->getIndustryByUUID($industryUUID);
        if (null === $industry) {
            return $this->formatErrorMessage('industry.uuid.notExists', [':uuid' => $industryUUID], 'industries');
        }

        return null;
    }

    private function validateNIP(?string $nip): ?string
    {
        if (null === $nip) {
            return $this->formatErrorMessage('company.nip.required');
        }

        $nip = preg_replace('/\D/', '', $nip ?? '');

        $errorMessage = NIPValidator::validate($nip);
        if (null !== $errorMessage) {
            return $this->formatErrorMessage($errorMessage, [], 'validators');
        }

        return null;
    }

    private function validateREGON(?string $regon): ?string
    {
        if (null === $regon) {
            return $this->formatErrorMessage('company.regon.required');
        }

        $regon = preg_replace('/\D/', '', $regon ?? '');

        $errorMessage = REGONValidator::validate($regon);
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

    private function validatePhone(?string $phone): ?string
    {
        if (null === $phone) {
            return $this->formatErrorMessage('company.contact.phone.required');
        }

        return null;
    }

    private function validateEmail(?string $email): ?string
    {
        if (null === $email) {
            return $this->formatErrorMessage('company.contact.email.required');
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
            return $this->formatErrorMessage('company.address.street.required');
        }

        return null;
    }

    private function validatePostcode(?string $postcode): ?string
    {
        if (null === $postcode) {
            return $this->formatErrorMessage('company.address.postcode.required');
        }

        return null;
    }

    private function validateCity(?string $city): ?string
    {
        if (null === $city) {
            return $this->formatErrorMessage('company.address.city.required');
        }

        return null;
    }

    private function validateCountry(?string $country): ?string
    {
        if (null === $country) {
            return $this->formatErrorMessage('company.address.country.required');
        }

        return null;
    }

    private function formatErrorMessage(string $translationKey, array $parameters = [], ?string $domain = 'companies'): string
    {
        return sprintf(
            '%s - %s %d',
            $this->translator->trans($translationKey, $parameters, $domain),
            $this->translator->trans('row'),
            $this->rowIndex
        );
    }
}
