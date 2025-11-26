<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Validator\Constraints\Event;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidNotificationEvents extends Constraint
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