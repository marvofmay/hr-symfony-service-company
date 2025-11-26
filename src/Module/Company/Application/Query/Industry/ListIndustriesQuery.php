<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Industry;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Domain\Entity\Industry;

final class ListIndustriesQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return Industry::getAttributes();
    }

    public function getRelations(): array
    {
        return Industry::getRelations();
    }
}
