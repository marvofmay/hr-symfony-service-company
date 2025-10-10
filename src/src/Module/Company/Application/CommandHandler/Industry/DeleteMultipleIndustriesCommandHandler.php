<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Application\Event\Industry\IndustryMultipleDeletedEvent;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\IndustryMultipleDeleter;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class DeleteMultipleIndustriesCommandHandler
{
    public function __construct(private IndustryMultipleDeleter $industryMultipleDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteMultipleIndustriesCommand $command): void
    {
        $this->industryMultipleDeleter->multipleDelete($command->industries);
        $this->eventDispatcher->dispatch(new IndustryMultipleDeletedEvent(
            $command->industries->map(fn (Industry $industry) => $industry->getUUID())->toArray(),
        ));
    }
}
