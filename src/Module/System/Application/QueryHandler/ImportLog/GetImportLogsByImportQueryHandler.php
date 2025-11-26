<?php

declare(strict_types=1);

namespace App\Module\System\Application\QueryHandler\ImportLog;

use App\Module\System\Application\Query\ImportLog\GetImportLogsByImportQuery;
use App\Module\System\Domain\Interface\ImportLog\ImportLogReaderInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetImportLogsByImportQueryHandler
{
    public function __construct(private ImportLogReaderInterface $importLogReaderRepository)
    {
    }

    public function __invoke(GetImportLogsByImportQuery $query): Collection
    {
        return $this->importLogReaderRepository->getImportLogsByImport($query->import);
    }
}
