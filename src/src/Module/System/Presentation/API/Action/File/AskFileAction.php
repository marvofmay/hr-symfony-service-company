<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\File;

use App\Common\Domain\Enum\FileKindEnum;
use App\Module\System\Application\Query\File\GetFileByNamePathAndKindQuery;
use App\Module\System\Domain\Entity\File;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AskFileAction
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(string $fileName, string $uploadFilePath, FileKindEnum $fileKindEnum): ?File
    {
        $handleStamp = $this->queryBus->dispatch(new GetFileByNamePathAndKindQuery($fileName, $uploadFilePath, $fileKindEnum));

        return $handleStamp->last(HandledStamp::class)->getResult();
    }
}