<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\ImportIndustriesCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Industry\ImportIndustriesFromXLSX;
use App\Module\Company\Domain\Service\Industry\IndustryMultipleCreator;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


readonly class ImportIndustriesCommandHandler
{
    public function __construct(
        private IndustryReaderInterface $industryReaderRepository,
        private IndustryMultipleCreator $industryMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private ImportLogMultipleCreator $importLogMultipleCreator,
        private UpdateImportAction $updateImportAction,
    ) {
    }

    public function __invoke(ImportIndustriesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportIndustriesFromXLSX(
            sprintf('%s/%s/%s', '/var/www/html', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->industryReaderRepository
        );
        $errors = $importer->validateBeforeImport();
        if (empty($errors)) {
            $this->industryMultipleCreator->multipleCreate($importer->import());
            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        } else {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);

            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('industry.import.error', [], 'industries') . ': ' . $error);
            }
        }
    }
}
