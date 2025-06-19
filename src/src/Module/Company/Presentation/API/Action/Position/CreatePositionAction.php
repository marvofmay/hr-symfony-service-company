<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Module\Company\Application\Command\Position\CreatePositionCommand;
use App\Module\Company\Application\Validator\Position\PositionValidator;
use App\Module\Company\Domain\DTO\Position\CreateDTO;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreatePositionAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private PositionValidator $positionValidator,
        private PositionReaderInterface $positionReaderRepository,
    )
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->positionValidator->isPositionNameAlreadyExists($createDTO->name);
            $this->positionReaderRepository->getPositionsByUUID($createDTO->departmentsUUID);

            $this->commandBus->dispatch(
                new CreatePositionCommand(
                    $createDTO->name,
                    $createDTO->description,
                    $createDTO->active,
                    $createDTO->getDepartmentsUUID()
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
