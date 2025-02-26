<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\ImportIndustriesCommand;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\ImportIndustriesFromXLSX;
use App\Module\Company\Domain\Service\Industry\IndustryService;

readonly class ImportIndustriesCommandHandler
{
    public function __construct(private IndustryService $industryService)
    {
    }

    public function __invoke(ImportIndustriesCommand $command): void
    {
        $industries = [];
        foreach ($command->data as $item) {
            $industry = new Industry();
            $industry->setName($item[ImportIndustriesFromXLSX::COLUMN_NAME]);
            $industry->setDescription($item[ImportIndustriesFromXLSX::COLUMN_DESCRIPTION]);

            $industries[] = $industry;
        }

        $this->industryService->saveIndustriesInDB($industries);
    }
}
