<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Event\Company\CompanyViewedEvent;
use App\Module\Company\Application\Query\Company\GetCompanyByUUIDQuery;
use App\Module\Company\Application\Query\Company\GetDescendantUUIDsByUUIDQuery;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetDescendantUUIDsByUUIDQueryHandler
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.company.query.get.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetDescendantUUIDsByUUIDQuery $query): array
    {
        $this->validate($query);
        $uuids = $this->companyReaderRepository->getAllDescendantUUIDs($query->companyUUID);
        $this->eventDispatcher->dispatch(new CompanyViewedEvent([
            Company::COLUMN_UUID => $query->companyUUID,
        ]));

        return $uuids;
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
