<?php

namespace App\Common\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class TransformerNotFoundException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?\Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
