<?php

namespace App\Module\Company\Domain\Interface\ContractType;

use Doctrine\Common\Collections\Collection;

interface ContractTypeMultipleDeleterInterface
{
    public function multipleDelete(Collection $contractTypes): void;
}