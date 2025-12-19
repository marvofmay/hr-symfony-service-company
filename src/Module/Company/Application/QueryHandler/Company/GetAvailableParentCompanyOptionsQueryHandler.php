<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Company;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Query\Company\GetAvailableParentCompanyOptionsQuery;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetAvailableParentCompanyOptionsQueryHandler
{
    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        #[AutowireIterator(tag: 'app.company.query.parent_company_options.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetAvailableParentCompanyOptionsQuery $query): array
    {
        $this->validate($query);

        return $this->companyReaderRepository->getAvailableParentCompanyOptions($query->companyUUID);
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
