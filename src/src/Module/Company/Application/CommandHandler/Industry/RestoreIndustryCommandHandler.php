<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Industry\RestoreIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryRestoredEvent;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Industry\IndustryRestorer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestoreIndustryCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly IndustryRestorer $industryRestorer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.industry.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestoreIndustryCommand $command): void
    {
        $this->validate($command);

        $industry = $this->industryReaderRepository->getDeletedIndustryByUUID($command->industryUUID);
        $this->industryRestorer->restore($industry);
        $this->eventDispatcher->dispatch(new IndustryRestoredEvent([
            RestoreIndustryCommand::INDUSTRY_UUID => $command->industryUUID,
        ]));
    }
}
