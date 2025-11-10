<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Event\Company\CompanyImportedEvent;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Factory\ImporterFactory;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ImportCompaniesCommandHandler
{
    public function __construct(
        private ImportReaderInterface $importReaderRepository,
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
        private ImporterFactory $importerFactory,
    ) {
    }

    public function __invoke(ImportCompaniesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->importUUID);

        $importer = $this->importerFactory->getImporter(ImportKindEnum::IMPORT_COMPANIES);
        $importer->setFilePath(sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()));

        $preparedRows = $importer->run($import);

        $multiEvent = new CompanyImportedEvent($preparedRows);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                CompanyAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()?->getEmployee()?->getUUID(),
            )
        );
    }
}
