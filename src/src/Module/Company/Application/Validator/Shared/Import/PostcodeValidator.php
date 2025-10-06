<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Shared\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_shared_validator')]
class PostcodeValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService) {}

    public function validate(array $row, array $additionalData = []): ?string
    {
        $postcode = $row[ImportEmployeesFromXLSX::COLUMN_POSTCODE] ?? null;
        if (null === $postcode) {
            return $this->messageService->get('employee.postcode.required', [], 'employees');
        }

        return null;
    }
}