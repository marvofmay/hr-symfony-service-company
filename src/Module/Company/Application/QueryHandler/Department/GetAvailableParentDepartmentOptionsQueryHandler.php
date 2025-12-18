<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Query\Department\GetAvailableParentDepartmentOptionsQuery;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetAvailableParentDepartmentOptionsQueryHandler
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        #[AutowireIterator(tag: 'app.department.query.parent_department_options.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetAvailableParentDepartmentOptionsQuery $query): array
    {
        $this->validate($query);

        return $this->departmentReaderRepository->getAvailableParentDepartmentOptions($query->companyUUID, $query->departmentUUID);
    }

    protected function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}
