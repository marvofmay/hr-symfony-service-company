<?php

declare(strict_types = 1);

namespace App\Common\Exception;

use Exception;

class NotFindByUUIDException extends Exception
{
    public function __construct(
        string $message = 'recordNotFoundByUUID',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}