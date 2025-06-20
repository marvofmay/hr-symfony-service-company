<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Query\Industry\GetIndustryByUUIDQuery;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class AskIndustryAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(string $uuid): array
    {
        try {
            $handledStamp = $this->queryBus->dispatch(new GetIndustryByUUIDQuery($uuid));

            return $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
