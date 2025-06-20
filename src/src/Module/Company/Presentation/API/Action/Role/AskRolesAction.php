<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Application\Query\Role\ListRolesQuery;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class AskRolesAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(QueryDTOInterface $queryDTO): array
    {
        try {
            $handledStamp = $this->queryBus->dispatch(new ListRolesQuery($queryDTO));

            return $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
