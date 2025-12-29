<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Position;

use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Application\Query\Position\GetPositionSelectOptionsQuery;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetPositionSelectOptionsQueryHandler
{
    public function __construct(
        private PositionReaderInterface $positionReaderRepository,
        #[AutowireIterator(tag: 'app.position.query.select_options.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(GetPositionSelectOptionsQuery $query): array
    {
        $this->validate($query);

        return $this->positionReaderRepository->getSelectOptionsByDepartment($query->departmentUUID);
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
