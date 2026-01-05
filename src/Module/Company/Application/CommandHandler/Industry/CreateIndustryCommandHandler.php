<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Application\Event\Industry\IndustryCreatedEvent;
use App\Module\Company\Domain\Service\Industry\IndustryCreator;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CreateIndustryCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly IndustryCreator $industryCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.industry.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreateIndustryCommand $command): void
    {
        $this->validate($command);

        $this->industryCreator->create(name: $command->name, description: $command->description);

        $this->eventDispatcher->dispatch(new IndustryCreatedEvent([
            CreateIndustryCommand::INDUSTRY_NAME => $command->name,
            CreateIndustryCommand::INDUSTRY_DESCRIPTION => $command->description,
        ]));
    }
}
