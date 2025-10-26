<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\ContractType;

use App\Module\Company\Application\Event\Event;
use App\Module\Company\Domain\Entity\ContractType;

class ContractTypeEvent extends Event
{
    public function getEntityClass(): string
    {
        return ContractType::class;
    }
}
