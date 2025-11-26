<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultiplePositionsCommand implements CommandInterface
{
    public const string POSITIONS_UUIDS = 'positionsUUIDs';

    public function __construct(public array $positionsUUIDs)
    {
    }
}
