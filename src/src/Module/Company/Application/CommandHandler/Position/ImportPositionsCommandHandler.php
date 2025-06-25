<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\ImportPositionsCommand;
use App\Module\Company\Application\Event\Position\PositionImportedEvent;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Service\Position\ImportPositionsFromXLSX;
use App\Module\Company\Domain\Service\Position\PositionMultipleCreator;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportPositionsCommandHandler
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionMultipleCreator $positionMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private ImportLogMultipleCreator $importLogMultipleCreator,
        private UpdateImportAction $updateImportAction,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ImportPositionsCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportPositionsFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->positionReaderRepository,
            $this->departmentReaderRepository
        );
        $errors = $importer->validateBeforeImport();
        if (empty($errors)) {
            $this->positionMultipleCreator->multipleCreate($importer->import());
            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
            $this->eventDispatcher->dispatch(new PositionImportedEvent($importer->import()));
        } else {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);

            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('position.import.error', [], 'positions').': '.$error);
            }
        }
    }
}
