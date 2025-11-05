<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Position\Import;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Common\XLSX\XLSXIterator;
use App\Module\Company\Domain\Enum\Position\PositionImportColumnEnum;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportLogKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Service\ImportLog\ImportLogMultipleCreator;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.importer')]
class ImportPositionsFromXLSX extends XLSXIterator
{
    private array $positions = [];
    private array $departments = [];

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ImportLogMultipleCreator $importLogMultipleCreator,
        private readonly UpdateImportAction $updateImportAction,
        private readonly MessageService $messageService,
        private readonly MessageBusInterface $eventBus,
        private readonly ImportPositionsReferenceLoader $importPositionsReferenceLoader,
        private readonly EntityReferenceCache $entityReferenceCache,
        private readonly ImportPositionsPreparer $importPositionsPreparer,
        private readonly PositionsImporter $positionsImporter,
        #[AutowireIterator(tag: 'app.position.import.validator')] private readonly iterable $importPositionsValidators,
    ) {
        parent::__construct($this->translator);
    }

    public function getType(): string
    {
        return ImportKindEnum::IMPORT_POSITIONS->value;
    }

    public function validateRow(array $row, int $index): array
    {
        $errorMessages = [];
        foreach ($this->importPositionsValidators as $validator) {
            $error = $validator->validate(
                $row,
                [
                    'positions' => $this->positions,
                    'departments' => $this->departments,
                ]
            );
            if (null !== $error) {
                $errorMessages[] = sprintf('%s - %s', $error, $this->messageService->get('row', [':index' => $index]));
            }
        }

        return $errorMessages;
    }

    public function groupPositions(): array
    {
        $groupedPositions = [];

        foreach ($this->worksheet->getRowIterator(2) as $row) {
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(true);

            $data = [];
            foreach ($cells as $cell) {
                $data[] = $cell->getValue();
            }

            [$name, $description, $active, $departmentInternalCode] = $data;

            $key = md5($name.$description.$active);
            if (!isset($groupedPositions[$key])) {
                $groupedPositions[$key] = [
                    PositionImportColumnEnum::POSITION_NAME->value => $name,
                    PositionImportColumnEnum::POSITION_DESCRIPTION->value => $description,
                    PositionImportColumnEnum::POSITION_ACTIVE->value => $active,
                    PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value => [],
                ];
            }

            if (!in_array($departmentInternalCode, $groupedPositions[$key][PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value])) {
                if (null !== $departmentInternalCode) {
                    $groupedPositions[$key][PositionImportColumnEnum::DEPARTMENT_INTERNAL_CODE->value][] = $departmentInternalCode;
                }
            }
        }

        $reindexGroupedPositions = [];
        foreach ($groupedPositions as $data) {
            $reindexGroupedPositions[$data[PositionImportColumnEnum::POSITION_NAME->value]] = $data;
        }

        return $reindexGroupedPositions;
    }

    public function run(Import $import): array
    {
        $this->importPositionsReferenceLoader->preload($this->import());
        $this->positions = $this->importPositionsReferenceLoader->positions;
        $this->departments = $this->importPositionsReferenceLoader->departments;

        $errors = $this->validateBeforeImport();
        if (!empty($errors)) {
            $this->updateImportAction->execute($import, ImportStatusEnum::FAILED);
            $this->importLogMultipleCreator->multipleCreate($import, $errors, ImportLogKindEnum::IMPORT_ERROR);
            foreach ($errors as $error) {
                $this->eventBus->dispatch(
                    new LogFileEvent(
                        $this->messageService->get('position.import.error', [], 'positions').': '.$error,
                        LogLevel::ERROR,
                        MonologChanelEnum::IMPORT
                    )
                );
            }
        } else {
            [, $positionNameMap] = $this->importPositionsPreparer->prepare($this->import(), $this->positions);
            $groupPositions = $this->groupPositions();

            $this->positionsImporter->save(
                positionNameMap: $positionNameMap,
                groupPositions: $groupPositions,
                existingPositions: $this->positions,
                existingDepartments: $this->departments
            );

            $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        }
        $this->entityReferenceCache->clear();

        return $this->import();
    }
}
