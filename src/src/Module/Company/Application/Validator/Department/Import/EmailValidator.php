<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\EmailValidator as Email;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class EmailValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $email = $row[DepartmentImportColumnEnum::EMAIL->value] ?? null;
        if (null !== $email) {
            $errorMessage = Email::validate($email);
            if (null !== $errorMessage) {
                return $this->messageService->get($errorMessage, [], 'validators');
            }
        }

        $internalCode = (string)$row[DepartmentImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value] ?? null;
        if (array_key_exists($email, $additionalData['emailsInternalCodes']) && $additionalData['emailsInternalCodes'][$email] !== $internalCode) {
            return $this->messageService->get('department.email.alreadyExists', [':email' => $email], 'departments');
        }

        return null;
    }
}
