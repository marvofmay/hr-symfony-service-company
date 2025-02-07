<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\Exception;

use Exception;

class NotFindByUUIDException extends Exception
{
    public function __construct(
        string $message = 'Record not found by uuid',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}