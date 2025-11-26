<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\Import;

use App\Module\System\Application\Query\Import\GetImportByFileQuery;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AskImportAction
{
    public function __construct(#[Autowire(service: 'query.bus')] private MessageBusInterface $queryBus)
    {
    }

    public function ask(File $file): ?Import
    {
        $handleStamp = $this->queryBus->dispatch(new GetImportByFileQuery($file));

        return $handleStamp->last(HandledStamp::class)->getResult();
    }
}
