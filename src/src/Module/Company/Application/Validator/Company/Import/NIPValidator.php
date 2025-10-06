<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\NIPValidator as NIP;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class NIPValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $nip = $row[ImportCompaniesFromXLSX::COLUMN_NIP] ?? null;
        if (null === $nip) {
            return $this->messageService->get('company.nip.required', [], 'companies');
        }

        $nip = preg_replace('/\D/', '', $nip);
        $errorMessage = NIP::validate($nip);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}