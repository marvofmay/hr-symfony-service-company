<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CompanyAggregateAbstract
{
    public function __construct(
        protected EventStoreCreator $eventStoreCreator,
        protected Security $security,
        protected SerializerInterface $serializer,
        protected EventDispatcherInterface $eventDispatcher,
        protected CompanyAggregateReaderInterface $companyAggregateReaderRepository,
    ) {}

    protected function storeAndDispatchEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    CompanyAggregate::class,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser()?->getEmployee()?->getUUID(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}