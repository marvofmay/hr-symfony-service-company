<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Application\Query\Employee\ListEmployeesQuery;;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AskEmployeesAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(QueryDTOInterface $queryDTO): array
    {
        $handledStamp = $this->queryBus->dispatch(new ListEmployeesQuery($queryDTO));

        return $handledStamp->last(HandledStamp::class)->getResult();
    }
}