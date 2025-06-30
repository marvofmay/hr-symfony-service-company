<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Entity\Company;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CreateCompanyCommandHandler
{
    public function __construct(
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function __invoke(CreateCompanyCommand $command): void
    {
        $companyAggregate = CompanyAggregate::create(
            $command->fullName,
            $command->nip,
            $command->regon,
            IndustryUUID::fromString($command->industryUUID),
            $command->active,
            Address::fromDTO($command->address),
            $command->shortName,
            $command->description,
            $command->parentCompanyUUID,
        );

        $events = $companyAggregate->pullEvents();
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    Company::class,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser()->getEmployee()?->getUUID(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
