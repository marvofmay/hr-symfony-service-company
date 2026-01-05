<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryDeletedEvent;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Industry\IndustryDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DeleteIndustryCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly IndustryDeleter $industryDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly IndustryReaderInterface $industryReaderRepository,
        #[AutowireIterator(tag: 'app.industry.delete.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteIndustryCommand $command): void
    {
        $this->validate($command);

        $industry = $this->industryReaderRepository->getIndustryByUUID($command->industryUUID);

        $this->industryDeleter->delete($industry);
        $this->eventDispatcher->dispatch(new IndustryDeletedEvent([
            DeleteIndustryCommand::INDUSTRY_UUID => $command->industryUUID,
        ]));
    }
}
