<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\ImportLog;

use App\Module\System\Application\Query\ImportLog\GetImportLogsByImportQuery;
use App\Module\System\Domain\Entity\Import;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class AskImportLogsAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(Import $import): Collection
    {
        $handleStamp = $this->queryBus->dispatch(new GetImportLogsByImportQuery($import));

        return $handleStamp->last(HandledStamp::class)->getResult();
    }
}