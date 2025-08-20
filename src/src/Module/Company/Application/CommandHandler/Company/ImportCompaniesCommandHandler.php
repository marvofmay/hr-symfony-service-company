<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
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
use App\Module\Company\Domain\Event\Company\CompanyMultipleImportedEvent;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ImportCompaniesCommandHandler
{
    public function __construct(
        private CompanyReaderInterface   $companyReaderRepository,
        private ImportReaderInterface    $importReaderRepository,
        private TranslatorInterface      $translator,
        private IndustryReaderInterface  $industryReaderRepository,
        private CacheInterface           $cache,
        private EventStoreCreator        $eventStoreCreator,
        private Security                 $security,
        private SerializerInterface      $serializer,
        private EventDispatcherInterface $eventDispatcher,
        private UpdateImportAction       $updateImportAction,
        private CompanyAggregateReaderInterface $companyAggregateReaderRepository,
    )
    {
    }

    public function __invoke(ImportCompaniesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportCompaniesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->industryReaderRepository,
            $this->cache,
        );

        $nipMap = [];
        $preparedRows = [];

        foreach ($importer->import() as $row) {
            $webSite = $row[ImportCompaniesFromXLSX::COLUMN_WEBSITE];
            if ($webSite instanceof RichText) {
                $row[ImportCompaniesFromXLSX::COLUMN_WEBSITE] = $webSite->getPlainText();
            }

            $nip = trim((string)$row[ImportCompaniesFromXLSX::COLUMN_NIP]);
            $existingCompany = $this->companyReaderRepository->getCompanyByNIP($nip);
            $row['_is_company_already_exists_with_nip'] = null !== $existingCompany;

            if (!isset($nipMap[$nip])) {
                if ($row['_is_company_already_exists_with_nip']) {
                    $nipMap[$nip] = CompanyUUID::fromString($existingCompany->getUUID()->toString());
                } else {
                    $nipMap[$nip] = CompanyUUID::generate();
                }
            }

            $row['_aggregate_uuid'] = $nipMap[$nip]->toString();
            $preparedRows[] = $row;
        }

        foreach ($preparedRows as $row) {
            $parentRaw = $row[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_NIP] ?? null;
            $parentUUID = null;
            if ($parentRaw !== null) {
                $parentNIP = trim((string)$parentRaw);
                if ($parentNIP !== '' && isset($nipMap[$parentNIP])) {
                    $parentUUID = $nipMap[$parentNIP];
                }
                if ($parentNIP !== '' && !isset($nipMap[$parentNIP])) {
                    $existingParentCompany = $this->companyReaderRepository->getCompanyByNIP($parentNIP);
                    $parentUUID = CompanyUUID::fromString($existingParentCompany->getUUID()->toString());
                }
            }

            $nip = trim((string)$row[ImportCompaniesFromXLSX::COLUMN_NIP]);
            $uuid = $nipMap[$nip];

            if (!$row['_is_company_already_exists_with_nip']) {
                $this->create($row, $nip, $uuid, $parentUUID);
            } else {
                $this->update($row, $nip, $uuid, $parentUUID);
            }
        }

        $multiEvent = new CompanyMultipleImportedEvent($preparedRows);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                CompanyAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()?->getEmployee()?->getUUID(),
            )
        );

        $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
    }

    private function create(array $row, string $nip, CompanyUUID $uuid, ?CompanyUUID $parentUUID)
    {
        $companyAggregate = CompanyAggregate::create(
            FullName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME]),
            NIP::fromString($nip),
            REGON::fromString((string)$row[ImportCompaniesFromXLSX::COLUMN_REGON]),
            IndustryUUID::fromString($row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID]),
            (bool)$row[ImportCompaniesFromXLSX::COLUMN_ACTIVE],
            new Address(
                $row[ImportCompaniesFromXLSX::COLUMN_STREET],
                $row[ImportCompaniesFromXLSX::COLUMN_POSTCODE],
                $row[ImportCompaniesFromXLSX::COLUMN_CITY],
                $row[ImportCompaniesFromXLSX::COLUMN_COUNTRY]
            ),
            Phones::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_PHONE]]),
            ShortName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_SHORT_NAME]),
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_INTERNAL_CODE],
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_DESCRIPTION],
            $parentUUID,
            $row[ImportCompaniesFromXLSX::COLUMN_EMAIL] ? Emails::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_EMAIL]]) : null,
            $row[ImportCompaniesFromXLSX::COLUMN_WEBSITE] ? Websites::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_WEBSITE]]) : null,
            $uuid
        );

        $events = $companyAggregate->pullEvents();
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

    private function update(array $row, string $nip, CompanyUUID $uuid, ?CompanyUUID $parentUUID)
    {
        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($row['_aggregate_uuid'])
        );

        $companyAggregate->update(
            FullName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME]),
            NIP::fromString($nip),
            REGON::fromString((string)$row[ImportCompaniesFromXLSX::COLUMN_REGON]),
            IndustryUUID::fromString($row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID]),
            (bool)$row[ImportCompaniesFromXLSX::COLUMN_ACTIVE],
            new Address(
                $row[ImportCompaniesFromXLSX::COLUMN_STREET],
                $row[ImportCompaniesFromXLSX::COLUMN_POSTCODE],
                $row[ImportCompaniesFromXLSX::COLUMN_CITY],
                $row[ImportCompaniesFromXLSX::COLUMN_COUNTRY]
            ),
            Phones::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_PHONE]]),
            ShortName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_SHORT_NAME]),
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_INTERNAL_CODE],
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_DESCRIPTION],
            $parentUUID,
            $row[ImportCompaniesFromXLSX::COLUMN_EMAIL] ? Emails::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_EMAIL]]) : null,
            $row[ImportCompaniesFromXLSX::COLUMN_WEBSITE] ? Websites::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_WEBSITE]]) : null,
            $uuid
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
