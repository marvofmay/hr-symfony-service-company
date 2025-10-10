<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class IndustryUUIDValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $industryUUID = $row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID] ?? null;
        if (empty($industryUUID)) {
            return $this->messageService->get('employee.industryUUID.required', [], 'companies');
        }

        $industryExists = isset($additionalData['industries'][$industryUUID]);
        if (!$industryExists) {
            return $this->messageService->get('industry.uuid.notExists', [':uuid' => $industryUUID], 'industries');
        }

        return null;
    }
}
