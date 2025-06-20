<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\DeleteMultiplePositionsCommand;
use App\Module\Company\Domain\Service\Position\PositionMultipleDeleter;

readonly class DeleteMultiplePositionsCommandHandler
{
    public function __construct(private PositionMultipleDeleter $multipleDeleter)
    {
    }

    public function __invoke(DeleteMultiplePositionsCommand $command): void
    {
        $this->multipleDeleter->multipleDelete($command->getPositions());
    }
}
