<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Role\ImportRolesCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\ImportRolesFromXLSX;
use App\Module\Company\Domain\Service\Role\RoleMultipleCreator;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LogLevel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class ImportRolesCommandHandler
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private RoleMultipleCreator $roleMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private ImportLogMultipleCreator $importLogMultipleCreator,
        private UpdateImportAction $updateImportAction,
        private MessageBusInterface $eventBus,
        private MessageService $messageService,
    ) {
    }

    public function __invoke(ImportRolesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportRolesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->roleReaderRepository
        );
        $errors = $importer->validateBeforeImport();
        if (empty($errors)) {
            $this->roleMultipleCreator->multipleCreate($importer->import());
            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        } else {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);

            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent($this->messageService->get('role.import.error', [], 'roles').': '.$error)
                );
            }
        }
    }
}
