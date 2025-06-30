<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Employee;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\Company\Domain\Entity\Employee;

final class ListEmployeesQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return Employee::getAttributes();
    }

    public function getRelations(): array
    {
        return Employee::getRelations();
    }
}
