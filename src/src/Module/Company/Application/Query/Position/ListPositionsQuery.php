<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Position;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Domain\Entity\Position;

class ListPositionsQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    protected function getAttributes(): array
    {
        return Position::getAttributes();
    }

    protected function getRelations(): array
    {
        return Position::getRelations();
    }
}
