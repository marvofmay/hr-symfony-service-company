<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeleteIndustryCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeleteIndustryCommand $command): void
    {
        $industry = $command->getIndustry();
        $this->entityManager->remove($industry);
        $this->entityManager->flush();
    }
}
