<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\REGONValidator as REGON;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class REGONValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $regon = (string)$row[ImportCompaniesFromXLSX::COLUMN_REGON] ?? null;
        if (empty($regon)) {
            return $this->messageService->get('company.regon.required', [], 'companies');
        }

        $regon = preg_replace('/\D/', '', $regon);
        $errorMessage = REGON::validate($regon);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}