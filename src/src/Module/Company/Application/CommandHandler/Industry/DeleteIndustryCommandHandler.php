<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

readonly class DeleteIndustryCommandHandler
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function __invoke(DeleteIndustryCommand $command): void
    {
        $this->industryWriterRepository->deleteIndustryInDB($command->getIndustry());
    }
}
