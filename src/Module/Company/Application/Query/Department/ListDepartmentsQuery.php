<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Department;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Domain\Entity\Department;

final class ListDepartmentsQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return Department::getAttributes();
    }

    public function getRelations(): array
    {
        return Department::getRelations();
    }
}
