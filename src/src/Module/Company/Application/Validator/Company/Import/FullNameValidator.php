<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class FullNameValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $fullName = $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME] ?? null;

        if (empty($fullName)) {
            return $this->messageService->get('company.fullName.required', [], 'companies');
        }

        if (strlen($fullName) < 3) {
            return $this->messageService->get('company.name.minimumLength', [':qty' => 3], 'companies');
        }

        return null;
    }
}
