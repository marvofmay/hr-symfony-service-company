<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\ImportEmployeesCommand;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Service\Employee\EmployeeMultipleCreator;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use App\Module\System\Domain\Interface\Import\ImportReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ImportEmployeesCommandHandler
{
    public function __construct(
        private EmployeeMultipleCreator $employeeMultipleCreator,
        private TranslatorInterface $translator,
        private EmployeeReaderInterface $employeeReaderRepository,
        private LoggerInterface $logger,
        private ImportReaderInterface $importReaderRepository,
    ) {}

    public function __invoke(ImportEmployeesCommand $command): void
    {
        $import = $this->importReaderRepository->getImportByUUID($command->getImportUUID());
        $importer = new ImportEmployeesFromXLSX(
            sprintf('%s/%s/%s', '/var/www/html', $import->getFile()->getFilePath(), $import->getFile()->getFileName()),
            $this->translator,
            $this->employeeReaderRepository
        );
        $importer->import();
        $errors = $importer->getErrors();;
        foreach ($errors as $error) {
            $this->logger->error($this->translator->trans('employee.import.error', [], 'employees') . ': ' . $error);
        }
        if (empty($errors)) {
            $this->employeeMultipleCreator->multipleCreate($importer->import());
            //ToDo:: send notification (success) to user in feature
            //ToDo:: save status (done) in "import" table
        } else {
            //ToDo:: send notification (failed) to user in feature
            //ToDo:: save status (failed) in "import" table and add errors ($errors) in "import_log" table
            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('employee.import.error', [], 'employees') . ': ' . $error);
            }
        }
    }
}
