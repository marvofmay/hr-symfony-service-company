<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryDeletedEvent;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\IndustryDeleter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class DeleteIndustryCommandHandler
{
    public function __construct(private IndustryDeleter $industryDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteIndustryCommand $command): void
    {
        $this->industryDeleter->delete($command->getIndustry());
        $this->eventDispatcher->dispatch(new IndustryDeletedEvent([
            Industry::COLUMN_UUID => $command->getIndustry()->getUUID(),
        ]));
    }
}
