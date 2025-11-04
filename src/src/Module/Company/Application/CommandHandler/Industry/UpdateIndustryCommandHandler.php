<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryUpdatedEvent;
use App\Module\Company\Domain\Service\Industry\IndustryUpdater;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class UpdateIndustryCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly IndustryUpdater $industryUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.industry.update.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(UpdateIndustryCommand $command): void
    {
        $this->validate($command);

        $this->industryUpdater->update($command);

        $this->eventDispatcher->dispatch(new IndustryUpdatedEvent([
            UpdateIndustryCommand::INDUSTRY_UUID        => $command->industryUUID,
            UpdateIndustryCommand::INDUSTRY_NAME        => $command->name,
            UpdateIndustryCommand::INDUSTRY_DESCRIPTION => $command->description,
        ]));
    }
}
