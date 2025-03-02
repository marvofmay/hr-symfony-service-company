<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\ImportIndustriesCommand;
use App\Module\Company\Domain\Service\Industry\IndustryMultipleCreator;


readonly class ImportIndustriesCommandHandler
{
    public function __construct(private IndustryMultipleCreator $industryMultipleCreator,)
    {
    }

    public function __invoke(ImportIndustriesCommand $command): void
    {
        $this->industryMultipleCreator->multipleCreate($command->data);
    }
}
