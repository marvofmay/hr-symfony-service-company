<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteMultipleIndustriesCommand;
use App\Module\Company\Domain\Service\Industry\IndustryMultipleDeleter;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer\IndustryWriterRepository;

readonly class DeleteMultipleIndustriesCommandHandler
{
    public function __construct(private IndustryMultipleDeleter $industryMultipleDeleter,)
    {
    }

    public function __invoke(DeleteMultipleIndustriesCommand $command): void
    {
        $this->industryMultipleDeleter->multipleDelete($command->industries);
    }
}
