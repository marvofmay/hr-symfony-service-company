<?php

namespace App\Module\Company\Application\Command\Position;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeletePositionCommand implements CommandInterface
{
    public const string POSITION_UUID = 'positionUUID';

    public function __construct(public string $positionUUID)
    {
    }
}
