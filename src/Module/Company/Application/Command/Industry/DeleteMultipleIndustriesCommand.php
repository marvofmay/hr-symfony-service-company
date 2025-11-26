<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Common\Domain\Interface\CommandInterface;

class DeleteMultipleIndustriesCommand implements CommandInterface
{
    public const string INDUSTRIES_UUIDS = 'industriesUUIDs';

    public function __construct(public array $industriesUUIDs)
    {
    }
}
