<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer\IndustryWriterRepository;

readonly class DeleteMultipleIndustriesCommandHandler
{
    public function __construct(private IndustryWriterRepository $industryWriterRepository)
    {
    }

    public function __invoke(DeleteMultipleIndustriesCommand $command): void
    {
        $this->industryWriterRepository->deleteMultipleIndustriesInDB($command->industries);
    }
}
