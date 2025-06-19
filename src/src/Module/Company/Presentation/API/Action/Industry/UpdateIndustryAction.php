<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\DTO\Industry\UpdateDTO;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateIndustryAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private IndustryReaderInterface $industryReaderRepository,)
    {
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateIndustryCommand(
                $updateDTO->getUUID(),
                $updateDTO->name,
                $updateDTO->description,
                $this->industryReaderRepository->getIndustryByUUID($updateDTO->getUUID())
            )
        );
    }
}
