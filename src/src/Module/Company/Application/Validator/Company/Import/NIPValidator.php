<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\NIPValidator as NIP;
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.company.import.validator')]
final readonly class NIPValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $nip = (string) $row[CompanyImportColumnEnum::NIP->value] ?? null;
        if (empty($nip)) {
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
