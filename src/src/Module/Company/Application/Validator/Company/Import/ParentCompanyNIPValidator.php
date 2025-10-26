<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\NIPValidator as NIP;
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.company.import.validator')]
class ParentCompanyNIPValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $parentCompanyNIP = (string) $row[CompanyImportColumnEnum::PARENT_COMPANY_NIP->value] ?? null;
        if (empty($parentCompanyNIP)) {
            return null;
        }

        $parentCompanyNIP = preg_replace('/\D/', '', $parentCompanyNIP);
        $errorMessage = NIP::validate($parentCompanyNIP);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}
