<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\IndustryUpdater;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class UpdateIndustryCommandHandler
{
    public function __construct(private IndustryUpdater $industryUpdater, private EventDispatcherInterface $eventDispatcher,)
    {
    }

    public function __invoke(UpdateIndustryCommand $command): void
    {
        $this->industryUpdater->update(
            $command->getIndustry(),
            $command->getName(),
            $command->getDescription()
        );

        $this->eventDispatcher->dispatch([
            Industry::COLUMN_UUID        => $command->getIndustry()->getUUID(),
            Industry::COLUMN_NAME        => $command->getName(),
            Industry::COLUMN_DESCRIPTION => $command->getDescription(),
        ]);
    }
}
