<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class NameValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_NAME] ?? null;

        if (empty($name)) {
            return $this->messageService->get('department.name.required', [], 'departments');
        }

        if (strlen($name) < 3) {
            return $this->messageService->get('department.name.minimumLength', [':qty' => 3], 'departments');
        }

        return null;
    }
}
