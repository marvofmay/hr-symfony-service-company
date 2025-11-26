<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position\Import;

use App\Common\Domain\Interface\ImportRowValidatorInterface;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.position.import.validator')]
final readonly class NameValidator implements ImportRowValidatorInterface
{
    public const int MINIMUM_LENGTH = 3;
    public const int MAXIMUM_LENGTH = 100;

    public function __construct(private readonly MessageService $messageService)
    {
    }

    public function validate(array $row, array $additionalData = []): ?string
    {
        $name = $row[PositionImportColumnEnum::POSITION_NAME->value] ?? null;

        if (empty($name)) {
            return $this->messageService->get('position.name.required', [], 'positions');
        }

        if (strlen($name) < self::MINIMUM_LENGTH) {
            return $this->messageService->get('position.name.minimumLength', [':qty' => self::MINIMUM_LENGTH], 'positions');
        }
        if (strlen($name) > self::MAXIMUM_LENGTH) {
            return $this->messageService->get('position.name.maximumLength', [':qty' => self::MAXIMUM_LENGTH], 'positions');
        }

        return null;
    }
}
