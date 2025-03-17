<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Action;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Note\Application\Query\ListNotesQuery;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AskNotesAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(QueryDTOInterface $queryDTO): array
    {
        $handledStamp = $this->queryBus->dispatch(new ListNotesQuery($queryDTO));

        return $handledStamp->last(HandledStamp::class)->getResult();
    }
}