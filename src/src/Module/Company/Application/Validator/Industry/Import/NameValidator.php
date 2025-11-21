<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Industry\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.industry.import.validator')]
final readonly class NameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;
    public const int MAXIMUM_LENGTH = 100;

    public function __construct(private MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[IndustryImportColumnEnum::INDUSTRY_NAME->value] ?? null;

        if (empty($name)) {
            return $this->messageService->get('industry.name.required', [], 'industries');
        }

        if (strlen($name) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('industry.name.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'industries');
        }
        if (strlen($name) > self::MAXIMUM_LENGTH) {
            return $this->messageService->get('industry.name.maximumLength', [':qty' => self::MAXIMUM_LENGTH], 'industries');
        }

        return null;
    }
}
