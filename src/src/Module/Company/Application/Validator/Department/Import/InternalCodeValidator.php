<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class InternalCodeValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $internalCode = $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_INTERNAL_CODE] ?? null;

        if (empty($internalCode)) {
            return $this->messageService->get('department.internalCode.required', [], 'departments');
        }

        if (strlen($internalCode) < 2) {
            return $this->messageService->get('department.internalCode.minimumLength', [':qty' => 2], 'departments');
        }

        return null;
    }
}
