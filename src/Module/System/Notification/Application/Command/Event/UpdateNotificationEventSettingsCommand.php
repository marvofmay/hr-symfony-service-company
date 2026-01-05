<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Command\Event;

use App\Common\Domain\Interface\CommandInterface;

final readonly class UpdateNotificationEventSettingsCommand implements CommandInterface
{
    public const string EVENT_NAMES = 'eventNames';

    public function __construct(public array $eventNames = [])
    {
    }
}
