<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Application\Event\Industry\IndustryMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Industry\IndustryMultipleDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeleteMultipleIndustriesCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly IndustryMultipleDeleter $industryMultipleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.industry.delete_multiple.validator')] protected iterable $validators,
    )
    {
    }

    public function __invoke(DeleteMultipleIndustriesCommand $command): void
    {
        $this->validate($command);

        $industries = $this->industryReaderRepository->getIndustriesByUUID($command->industriesUUIDs);
        $this->industryMultipleDeleter->multipleDelete($industries);

        $this->eventDispatcher->dispatch(new IndustryMultipleDeletedEvent([
            DeleteMultipleIndustriesCommand::INDUSTRIES_UUIDS => $command->industriesUUIDs,
        ]));
    }
}
