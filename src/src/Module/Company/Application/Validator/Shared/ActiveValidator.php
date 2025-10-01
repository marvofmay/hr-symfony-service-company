<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Shared;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Shared\Utils\BoolValidator;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_shared_validator')]
class ActiveValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $active = $row[ImportEmployeesFromXLSX::COLUMN_ACTIVE] ?? false;
        $errorMessage = BoolValidator::validate($active);
        if (null !== $errorMessage) {
            return $this->messageService->get($errorMessage, [], 'validators');
        }

        return null;
    }
}