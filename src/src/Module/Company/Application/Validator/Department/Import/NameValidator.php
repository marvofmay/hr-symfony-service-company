<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class NameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[DepartmentImportColumnEnum::DEPARTMENT_NAME->value] ?? null;

        if (empty($name)) {
            return $this->messageService->get('department.name.required', [], 'departments');
        }

        if (strlen($name) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('department.name.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'departments');
        }

        return null;
    }
}
