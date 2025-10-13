<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\NIPValidator as NIP;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class ParentCompanyNIPValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentCompanyNIP = (string) $row[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_NIP] ?? null;
        if (empty($parentCompanyNIP)) {
            return null;
        }

        $parentCompanyNIP = preg_replace('/\D/', '', $parentCompanyNIP);
        $errorMessage = NIP::validate($parentCompanyNIP);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        // $companyExists = array_key_exists($parentCompanyNIP, $additionalData['companies']);
        // if (!$companyExists) {
        //   return $this->messageService->get('company.nip.notExists', [':nip' => $parentCompanyNIP], 'companies');
        // }

        return null;
    }
}
