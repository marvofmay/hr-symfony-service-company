<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\EmailValidator as Email;
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_company_validator')]
class EmailValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $email = $row[CompanyImportColumnEnum::EMAIL->value] ?? null;
        if (null !== $email) {
            $errorMessage = Email::validate($email);
            if (null !== $errorMessage) {
                return $this->messageService->get($errorMessage, [], 'validators');
            }
        }

        $nip = (string)$row[CompanyImportColumnEnum::NIP->value] ?? null;
        if (array_key_exists($email, $additionalData['emailsNIPs']) && $additionalData['emailsNIPs'][$email] !== $nip) {
            return $this->messageService->get('company.email.alreadyExists', [':email' => $email], 'companies');
        }

        return null;
    }
}
