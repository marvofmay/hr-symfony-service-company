<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFindByUUIDException extends \Exception
{
    public function __construct(
        string $message = 'recordNotFoundByUUID',
        int $code = Response::HTTP_NOT_FOUND,
        ?\Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
