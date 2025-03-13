<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\ContractType;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Domain\Entity\ContractType;

class ListContractTypesQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    protected function getAttributes(): array
    {
        return ContractType::getAttributes();
    }

    protected function getRelations(): array
    {
        return ContractType::getRelations();
    }
}
