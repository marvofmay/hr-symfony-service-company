<?php

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\DeleteCompanyCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteCompanyCommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private CompanyAggregateReaderInterface $companyAggregateReaderRepository,
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
    )
    {
    }

    public function __invoke(DeleteCompanyCommand $command): void
    {
        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($command->getCompany()->getUUID()->toString())
        );

        $companyAggregate->delete();

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
