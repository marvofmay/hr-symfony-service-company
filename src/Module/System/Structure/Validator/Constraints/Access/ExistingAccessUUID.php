<?php

namespace App\Module\System\Structure\Validator\Constraints\Access;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExistingAccessUUID extends Constraint
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
