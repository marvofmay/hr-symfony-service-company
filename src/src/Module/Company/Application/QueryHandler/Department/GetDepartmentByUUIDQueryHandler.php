<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Department;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\Department\DepartmentViewedEvent;
use App\Module\Company\Application\Query\Department\GetDepartmentByUUIDQuery;
use App\Module\Company\Application\Transformer\Department\DepartmentDataTransformer;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetDepartmentByUUIDQueryHandler
{
    public function __construct(
        private DepartmentReaderInterface $departmentReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.department.query.get.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetDepartmentByUUIDQuery $query): array
    {
        $this->validate($query);

        $department = $this->departmentReaderRepository->getDepartmentByUUID($query->departmentUUID);
        $transformer = new DepartmentDataTransformer();

        $this->eventDispatcher->dispatch(new DepartmentViewedEvent([
            Department::COLUMN_UUID => $query->departmentUUID,
        ]));

        return $transformer->transformToArray($department);
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
