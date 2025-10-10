<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\UpdateCompanyCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\FullName;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\ShortName;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateCompanyCommandHandler
{
    public function __construct(
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
        private EventDispatcherInterface $eventDispatcher,
        private CompanyAggregateReaderInterface $companyAggregateReaderRepository,
    ) {
    }

    public function __invoke(UpdateCompanyCommand $command): void
    {
        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($command->company->getUUID()->toString())
        );

        $companyAggregate->update(
            FullName::fromString($command->fullName),
            NIP::fromString($command->nip),
            REGON::fromString($command->regon),
            IndustryUUID::fromString($command->industryUUID),
            $command->active,
            Address::fromDTO($command->address),
            Phones::fromArray($command->phones),
            ShortName::fromString($command->shortName),
            $command->internalCode,
            $command->description,
            $command->parentCompanyUUID ? CompanyUUID::fromString($command->parentCompanyUUID) : null,
            $command->emails ? Emails::fromArray($command->emails) : null,
            $command->websites ? Websites::fromArray($command->websites) : null,
        );

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
