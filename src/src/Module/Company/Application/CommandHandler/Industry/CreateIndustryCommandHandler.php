<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\IndustryCreator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CreateIndustryCommandHandler
{
    public function __construct(private IndustryCreator $industryCreator, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(CreateIndustryCommand $command): void
    {
        $this->industryCreator->create($command->name, $command->description);
        $this->eventDispatcher->dispatch(new IndustryCreatedEvent([
            Industry::COLUMN_NAME => $command->name,
            Industry::COLUMN_DESCRIPTION => $command->description,
        ]));
    }
}
