<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Employee;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\Employee\EmployeeViewedEvent;
use App\Module\Company\Application\Query\Employee\GetEmployeeByUUIDQuery;
use App\Module\Company\Application\Transformer\Employee\EmployeeDataTransformer;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetEmployeeByUUIDQueryHandler
{
    public function __construct(
        private EmployeeReaderInterface $employeeReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.employee.query.get.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetEmployeeByUUIDQuery $query): array
    {
        $this->validate($query);

        $employee = $this->employeeReaderRepository->getEmployeeByUUID($query->employeeUUID);
        $transformer = new EmployeeDataTransformer();

        $this->eventDispatcher->dispatch(new EmployeeViewedEvent([
            Company::COLUMN_UUID => $query->employeeUUID,
        ]));

        return $transformer->transformToArray($employee);
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
