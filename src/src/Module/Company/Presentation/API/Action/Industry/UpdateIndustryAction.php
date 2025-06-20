<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Application\Validator\Industry\IndustryValidator;
use App\Module\Company\Domain\DTO\Industry\UpdateDTO;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateIndustryAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private IndustryReaderInterface $industryReaderRepository,
        private IndustryValidator $industryValidator,
    ) {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        try {
            $industry = $this->industryReaderRepository->getIndustryByUUID($uuid);
            $this->industryValidator->isIndustryNameAlreadyExists($updateDTO->name, $uuid);

            $this->commandBus->dispatch(new UpdateIndustryCommand($updateDTO->name, $updateDTO->description, $industry));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
