<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\EventLog;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'app.loggable.event')]
interface LoggableEventInterface
{
    public function getEntityClass(): string;

    public function getData(): array;
}
