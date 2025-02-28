<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer\IndustryWriterRepository;

readonly class CreateIndustryCommandHandler
{
    public function __construct(private Industry $industry, private IndustryWriterRepository $industryWriterRepository)
    {
    }

    public function __invoke(CreateIndustryCommand $command): void
    {
        $this->industry->setName($command->name);
        $this->industry->setDescription($command->description);

        $this->industryWriterRepository->saveIndustryInDB($this->industry);
    }
}
