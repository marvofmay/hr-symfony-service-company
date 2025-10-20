<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.company.import.validator')]
class FullNameValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $fullName = $row[CompanyImportColumnEnum::COMPANY_FULL_NAME->value] ?? null;

        if (empty($fullName)) {
            return $this->messageService->get('company.fullName.required', [], 'companies');
        }

        if (strlen($fullName) < 3) {
            return $this->messageService->get('company.fullName.minimumLength', [':qty' => 3], 'companies');
        }

        return null;
    }
}
