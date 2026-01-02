<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Module\Company\Application\Query\Department\GetDepartmentSelectOptionsQuery;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetDepartmentSelectOptionsQueryHandler
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
    ) {
    }

    public function __invoke(GetDepartmentSelectOptionsQuery $query): array
    {
        return $this->departmentReaderRepository->getSelectOptions();
    }
}
