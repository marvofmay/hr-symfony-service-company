<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\DeleteMultipleCompaniesCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Event\Company\CompanyMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteMultipleCompaniesCommandHandler
{
    public function __construct(
        private EventDispatcherInterface        $eventDispatcher,
        private CompanyAggregateReaderInterface $companyAggregateReaderRepository,
        private EventStoreCreator               $eventStoreCreator,
        private Security                        $security,
        private SerializerInterface             $serializer,
    )
    {
    }

    public function __invoke(DeleteMultipleCompaniesCommand $command): void
    {
        $deletedUUIDs = [];
        foreach ($command->companies as $company) {
            $uuid = CompanyUUID::fromString($company->getUUID()->toString());
            $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID($uuid);

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

            $deletedUUIDs[] = $uuid->toString();
        }

        $multiEvent = new CompanyMultipleDeletedEvent($deletedUUIDs);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                CompanyAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()->getEmployee()?->getUUID(),
            )
        );
    }
}
