<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Company;

use App\Module\Company\Application\Command\Company\ImportCompaniesCommand;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Service\Company\CompanyMultipleCreator;
use App\Module\Company\Domain\Service\Company\ImportCompaniesFromXLSX;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ImportCompaniesCommandHandler
{
    public function __construct(
        private CompanyMultipleCreator $companyMultipleCreator,
        private TranslatorInterface $translator,
        private CompanyReaderInterface $companyReaderRepository,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ImportCompaniesCommand $command): void
    {
        $importer = new ImportCompaniesFromXLSX(
            sprintf('%s/%s/%s', '/var/www/html', $command->getUploadFilePath(), $command->getFileName()),
            $this->translator,
            $this->companyReaderRepository
        );
        $importer->import();
        $errors = $importer->getErrors();

        if (empty($errors)) {
            $this->companyMultipleCreator->multipleCreate($importer->import());
            //ToDo:: send notification (success) to user in feature
            //ToDo:: save status (done) in "import" table
        } else {
            //ToDo:: send notification (failed) to user in feature
            //ToDo:: save status (failed) in "import" table and add errors ($errors) in "import_log" table
            foreach ($errors as $error) {
                $this->logger->error($this->translator->trans('company.import.error', [], 'companies') . ': ' . $error);
            }
        }
    }
}
