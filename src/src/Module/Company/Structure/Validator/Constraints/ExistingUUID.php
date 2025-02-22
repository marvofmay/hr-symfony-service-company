<?php

namespace App\Module\Company\Structure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExistingUUID extends Constraint
{
    public array $message;
    public string $domain;

    public function __construct(array $message = [], string $domain = 'messages', ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        $this->message = $message;
        $this->domain = $domain;
    }
}
