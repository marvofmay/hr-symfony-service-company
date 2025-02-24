<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Service\Industry\IndustryService;
readonly class CreateIndustryCommandHandler
{
    public function __construct(private IndustryService $roleService)
    {
    }

    public function __invoke(CreateIndustryCommand $command): void
    {
        $role = new Industry();
        $role->setName($command->name);
        $role->setDescription($command->description);

        $this->roleService->saveIndustryInDB($role);
    }
}
