<?php

declare(strict_types=1);

namespace App\Module\System\Application\Transformer\ImportLog;

use Doctrine\Common\Collections\Collection;

class ImportLogErrorTransformer
{
    public static function map(Collection $importLogs): array
    {
        $result = [];
        foreach (array_values($importLogs->toArray()) as $importLog) {
            $result[] = $importLog->getData();
        }

        return $result;
    }
}