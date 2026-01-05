<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifiable.event')]
interface NotifiableEventInterface
{
}
