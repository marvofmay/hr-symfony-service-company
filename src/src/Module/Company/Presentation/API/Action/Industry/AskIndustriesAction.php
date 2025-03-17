<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Application\Query\Industry\ListIndustriesQuery;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AskIndustriesAction
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(QueryDTOInterface $queryDTO): ?array
    {
        $handledStamp = $this->queryBus->dispatch(new ListIndustriesQuery($queryDTO));

        return $handledStamp->last(HandledStamp::class)->getResult();
    }
}