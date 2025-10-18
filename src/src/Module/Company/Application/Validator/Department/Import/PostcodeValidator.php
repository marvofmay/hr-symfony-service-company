<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\DepartmentImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.import_department_validator')]
class PostcodeValidator implements ImportRowValidatorInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $postcode = $row[DepartmentImportColumnEnum::POSTCODE->value] ?? null;
        if (null === $postcode) {
            return $this->messageService->get('department.postcode.required', [], 'departments');
        }

        return null;
    }
}
