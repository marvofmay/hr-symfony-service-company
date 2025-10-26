<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Position;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Application\Command\Position\DeletePositionCommand;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.position.restore.validator')]
final readonly class PositionAlreadyDeletedValidator implements ValidatorInterface
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
        if (!property_exists($data, DeletePositionCommand::POSITION_UUID)) {
            return;
        }

        $positionUUID = $data->{DeletePositionCommand::POSITION_UUID};
        $positionDeleted = $this->positionReaderRepository->getDeletedPositionByUUID($positionUUID);
        if (null === $positionDeleted) {
            throw new \Exception($this->translator->trans('position.deleted.notExists', [':uuid' => $positionUUID], 'positions'), Response::HTTP_CONFLICT);
        }
    }
}
