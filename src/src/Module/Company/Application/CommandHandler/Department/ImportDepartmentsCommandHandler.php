<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Module\Company\Application\Command\Department\ImportDepartmentsCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Service\Department\DepartmentMultipleCreator;
use App\Module\Company\Domain\Service\Department\ImportDepartmentsFromXLSX;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use App\Module\System\Presentation\API\Action\Import\UpdateImportAction;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportDepartmentsCommandHandler
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentMultipleCreator $departmentMultipleCreator,
        private ImportReaderInterface $importReaderRepository,
        private TranslatorInterface $translator,
        private UpdateImportAction $updateImportAction,
        private CacheInterface $cache,
    ) {
    }

    public function __invoke(ImportDepartmentsCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportDepartmentsFromXLSX(
            sprintf('%s/%s', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->companyReaderRepository,
            $this->departmentReaderRepository,
            $this->cache,
        );

        $this->departmentMultipleCreator->multipleCreate($importer->import());
        $this->updateImportAction->execute($import, ImportStatusEnum::DONE);
        // ToDo save notification about DONE import - immediately - if checked in settings
    }
}
