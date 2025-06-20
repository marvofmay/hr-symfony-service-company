<?php

declare(strict_types=1);

namespace App\Module\System\Application\Transformer\File;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class UploadFileErrorTransformer
{
    public static function map(ConstraintViolationListInterface $errors): array
    {
        return array_map(
            fn ($e) => [
                'field' => $e->getPropertyPath(),
                'message' => $e->getMessage(),
            ], iterator_to_array($errors)
        );
    }
}
