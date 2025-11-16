<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Interface\Event;

use App\Common\Domain\Interface\NotifiableEventInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'app.notifiable.event.payload')]
interface NotificationEventPayloadProviderInterface
{
    public function supports(string $notifiableEventName): bool;

    /**
     * Return array [$payload, $recipientUUIDs]
     */
    public function provide(NotifiableEventInterface $notifiableEvent): array;
}