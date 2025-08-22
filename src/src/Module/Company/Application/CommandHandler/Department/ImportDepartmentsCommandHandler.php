<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\ImportDepartmentsCommand;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Event\Department\DepartmentMultipleImportedEvent;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Factory\ImporterFactory;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ImportDepartmentsCommandHandler
{
    public function __construct(
        private ImportReaderInterface $importReaderRepository,
        private EventStoreCreator     $eventStoreCreator,
        private Security              $security,
        private SerializerInterface   $serializer,
        private ImporterFactory       $importerFactory,
    ) {
    }

    public function __invoke(ImportDepartmentsCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = $this->importerFactory->getImporter(
            ImportKindEnum::IMPORT_DEPARTMENTS,
            $import->getFile()->getFilePath(),
            $import->getFile()->getFileName()
        );
        $preparedRows = $importer->run($import);

        $multiEvent = new DepartmentMultipleImportedEvent($preparedRows);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                DepartmentAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()?->getEmployee()?->getUUID(),
            )
        );
    }
}
