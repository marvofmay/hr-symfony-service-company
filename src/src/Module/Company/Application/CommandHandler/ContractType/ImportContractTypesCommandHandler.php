<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\ImportContractTypesCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Service\ContractType\ContractTypeMultipleCreator;
use App\Module\Company\Domain\Service\ContractType\ImportContractTypesFromXLSX;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportContractTypesCommandHandler
{
    public function __construct(
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private ContractTypeMultipleCreator $contractTypeMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private LoggerInterface $logger,
        private ImportLogMultipleCreator $importLogMultipleCreator,
        private UpdateImportAction $updateImportAction,
    ) {
    }

    public function __invoke(ImportContractTypesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportContractTypesFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->contractTypeReaderRepository
        );
        $errors = $importer->validateBeforeImport();
        if (empty($errors)) {
            $this->contractTypeMultipleCreator->multipleCreate($importer->import());
            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        } else {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);

            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('contractType.import.error', [], 'contract_types').': '.$error);
            }
        }
    }
}
