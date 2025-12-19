<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Employee;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Query\Employee\GetAvailableParentEmployeeOptionsQuery;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetAvailableParentEmployeeOptionsQueryHandler
{
    public function __construct(
        private EmployeeReaderInterface $employeeReaderRepository,
        #[AutowireIterator(tag: 'app.employee.query.parent_employee_options.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetAvailableParentEmployeeOptionsQuery $query): array
    {
        $this->validate($query);

        return $this->employeeReaderRepository->getAvailableParentEmployeeOptions(
            companyUUID: $query->companyUUID,
            employeeUUID: $query->employeeUUID,
            departmentUUID: $query->departmentUUID
        );
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
