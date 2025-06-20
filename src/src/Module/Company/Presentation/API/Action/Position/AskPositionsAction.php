<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Position;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Application\Query\Position\ListPositionsQuery;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class AskPositionsAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(QueryDTOInterface $queryDTO): array
    {
        try {
            $handledStamp = $this->queryBus->dispatch(new ListPositionsQuery($queryDTO));

            return $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
