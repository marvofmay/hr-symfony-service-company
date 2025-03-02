<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Position;

use App\Module\Company\Application\Command\Position\ImportPositionsCommand;
use App\Module\Company\Domain\Service\Position\PositionMultipleCreator;


readonly class ImportPositionsCommandHandler
{
    public function __construct(private PositionMultipleCreator $positionMultipleCreator)
    {
    }

    public function __invoke(ImportPositionsCommand $command): void
    {
        $this->positionMultipleCreator->multipleCreate($command->data);
    }
}
