<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class InternalCodeValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 2;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $internalCode = $row[DepartmentImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value] ?? null;

        if (empty($internalCode)) {
            return $this->messageService->get('department.internalCode.required', [], 'departments');
        }

        if (strlen($internalCode) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('department.internalCode.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'departments');
        }

        return null;
    }
}
