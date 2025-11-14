<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Common\XLSX\XLSXIterator;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportLogKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.importer')]
class ImportIndustriesFromXLSX extends XLSXIterator
{
    private array $industries = [];

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly UpdateImportAction $updateImportAction,
        private readonly MessageService $messageService,
        #[Autowire(service: 'event.bus')] private MessageBusInterface $eventBus,
        private readonly ImportIndustriesReferenceLoader $importIndustriesReferenceLoader,
        private readonly EntityReferenceCache $entityReferenceCache,
        private readonly ImportIndustriesPreparer $importIndustriesPreparer,
        private readonly IndustriesImporter $industriesImporter,
        #[AutowireIterator(tag: 'app.industry.import.validator')] private readonly iterable $importIndustriesValidators,
    ) {
        parent::__construct($this->translator);
    }

    public function getType(): string
    {
        return ImportKindEnum::IMPORT_INDUSTRIES->value;
    }

    public function validateRow(array $row, int $index): array
    {
        $errorMessages = [];
        
        foreach ($this->importIndustriesValidators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'industries' => $this->industries,
                ]
            );
            if (null !== $error) {
                $errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $errorMessages;
    }

    public function run(Import $import): array
    {
        $this->importIndustriesReferenceLoader->preload($this->import());
        $this->industries = $this->importIndustriesReferenceLoader->industries;

        $errors = $this->validateBeforeImport();
        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent(
                        $this->messageService->get('industry.import.error', [], 'positions').': '.$error,
                        LogLevel::ERROR,
                        MonologChanelEnum::IMPORT
                    )
                );
            }
        } else {
            $preparedRows = $this->importIndustriesPreparer->prepare($this->import(), $this->industries);
            $this->industriesImporter->save(preparedRows: $preparedRows, existingIndustries: $this->industries);

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }
        $this->entityReferenceCache->clear();

        return $this->import();
    }
}
