<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.department.import.validator')]
final readonly class DescriptionValidator implements ImportRowValidatorInterface
{
    public const int MIN_LENGTH = 30;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $description = $row[DepartmentImportColumnEnum::DEPARTMENT_DESCRIPTION->value] ?? null;
        if (strlen($description) < self::MIN_LENGTH) {
            return $this->messageService->get('department.description.minimumLength', [':qty' => self::MIN_LENGTH], 'departments');
        }

        return null;
    }
}
