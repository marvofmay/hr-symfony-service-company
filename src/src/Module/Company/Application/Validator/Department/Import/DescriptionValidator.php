<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class DescriptionValidator implements ImportRowValidatorInterface
{
    public const MIN_LENGTH = 30;
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $description = $row[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_DESCRIPTION] ?? null;
        if (strlen($description) < self::MIN_LENGTH) {
            return $this->messageService->get('department.description.minimumLength', [':qty' => self::MIN_LENGTH], 'departments');
        }

        return null;
    }
}
