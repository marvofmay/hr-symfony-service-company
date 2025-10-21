<?php

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\RestoreCompanyCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestoreCompanyCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CompanyAggregateReaderInterface $companyAggregateReaderRepository,
        private readonly EventStoreCreator $eventStoreCreator,
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        #[AutowireIterator(tag: 'app.company.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestoreCompanyCommand $command): void
    {
        $this->validate($command);

        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($command->companyUUID)
        );

        $companyAggregate->restore();

        $events = $companyAggregate->pullEvents();
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    CompanyAggregate::class,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser()->getEmployee()?->getUUID(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
