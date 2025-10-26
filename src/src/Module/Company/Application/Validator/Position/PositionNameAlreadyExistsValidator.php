<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.position.create.validator')]
#[AutoconfigureTag('app.position.update.validator')]
final readonly class PositionNameAlreadyExistsValidator implements ValidatorInterface
{
    public function __construct(private PositionReaderInterface $positionReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'name')) {
            return;
        }

        $name = $data->name;
        $positionUUID = $data->positionUUID ?? null;
        if ($this->positionReaderRepository->isPositionNameAlreadyExists($name, $positionUUID)) {
            throw new \Exception($this->translator->trans('position.name.alreadyExists', [':name' => $name], 'positions'), Response::HTTP_CONFLICT);
        }
    }
}
