<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\EmailValidator as Email;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_employee_validator')]
class EmailValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $email = $row[ImportCompaniesFromXLSX::COLUMN_EMAIL] ?? null;
        if (null !== $email) {
            $errorMessage = Email::validate($email);
            if (null !== $errorMessage) {
                return $this->messageService->get($errorMessage, [], 'validators');
            }
        }

        return null;
    }
}
