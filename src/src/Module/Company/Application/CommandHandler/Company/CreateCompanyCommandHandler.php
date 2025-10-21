<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
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
use App\Module\System\Application\Event\LogFileEvent;
use Psr\Log\LogLevel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateCompanyCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly EventStoreCreator $eventStoreCreator,
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageBusInterface $eventBus,
        #[AutowireIterator(tag: 'app.company.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreateCompanyCommand $command): void
    {
        $this->validate($command);

        $companyAggregate = CompanyAggregate::create(
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
            $employeeUUID = $this->security->getUser()->getEmployee()?->getUUID();
            $serializedEvent = $this->serializer->serialize($event, 'json');

            $message = sprintf(
                "company created ---> uuid: %s, eventClass: %s, aggregateClass: %s, data: %s, employeeUUID: %s",
                $event->uuid->toString(),
                $event::class,
                CompanyAggregate::class,
                $serializedEvent,
                $employeeUUID
            );

            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    CompanyAggregate::class,
                    $serializedEvent,
                    $employeeUUID
                )
            );

            $this->eventDispatcher->dispatch($event);
            $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::INFO, MonologChanelEnum::EVENT_STORE));
        }
    }
}
