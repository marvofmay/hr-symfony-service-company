<?php

declare(strict_types=1);

namespace App\Module\System\Application\Transformer\ImportLog;

use Doctrine\Common\Collections\Collection;

class ImportLogErrorTransformer
{
    public static function map(Collection $importLogs): array
    {
        $result = [];
        foreach (array_values($importLogs->toArray()) as $index => $importLog) {
            $result[] = [
                'field' => $index,
                'error' => $importLog->getData(),
            ];
        }

        return $result;
    }
}