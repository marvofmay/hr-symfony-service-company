<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use Doctrine\Common\Collections\Collection;

class DeleteMultipleContractTypesCommand
{
    public function __construct(public Collection $contractTypes)
    {
    }
}
