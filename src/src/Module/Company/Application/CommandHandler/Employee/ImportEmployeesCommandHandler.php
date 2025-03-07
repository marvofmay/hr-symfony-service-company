<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Module\Company\Application\Command\Employee\ImportEmployeesCommand;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Service\Employee\EmployeeMultipleCreator;
use App\Module\Company\Domain\Service\Employee\ImportEmployeesFromXLSX;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportEmployeesCommandHandler
{
    public function __construct(
        private EmployeeMultipleCreator $employeeMultipleCreator,
        private TranslatorInterface $translator,
        private EmployeeReaderInterface $employeeReaderRepository,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ImportEmployeesCommand $command): void
    {
        $importer = new ImportEmployeesFromXLSX(
            sprintf('%s/%s/%s', '/var/www/html', $command->getUploadFilePath(), $command->getFileName()),
            $this->translator,
            $this->employeeReaderRepository
        );
        $errors = $importer->getErrors();

        if (empty($errors)) {
            $this->employeeMultipleCreator->multipleCreate($importer->import());
            //ToDo:: send notification (success) to user in feature
            //ToDo:: save status (done) errors (mull) in "import_log" table in feature
        } else {
            //ToDo:: send notification (failed) to user in feature
            //ToDo:: save status (failed) errors ($errors) in "import_log" table in feature
            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('employee.import.error', [], 'employees') . ': ' . $error);
            }
        }
    }
}
