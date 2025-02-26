<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Domain\Service\Industry\IndustryService;

readonly class DeleteMultipleIndustriesCommandHandler
{
    public function __construct(private IndustryService $industryService)
    {
    }

    public function __invoke(DeleteMultipleIndustriesCommand $command): void
    {
        $this->industryService->deleteMultipleIndustriesInDB($command->selectedUUID);
    }
}
