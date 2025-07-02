<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Company\CompanyMultipleCreator;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ImportCompaniesCommandHandler
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        private CompanyMultipleCreator $companyMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private UpdateImportAction $updateImportAction,
        private IndustryReaderInterface $industryReaderRepository,
        private CacheInterface $cache,
    ) {
    }

    public function __invoke(ImportCompaniesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportCompaniesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->companyReaderRepository,
            $this->industryReaderRepository,
            $this->cache,
        );

        $this->companyMultipleCreator->multipleCreate($importer->import());
        $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        // ToDo save notification about DONE import - immediately
    }
}
