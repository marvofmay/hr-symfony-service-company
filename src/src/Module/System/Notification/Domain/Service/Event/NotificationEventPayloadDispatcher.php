<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Event;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Common\Domain\Trait\ClassNameExtractorTrait;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class NotificationEventPayloadDispatcher
{
    use ClassNameExtractorTrait;

    public function __construct(#[AutowireIterator(tag: 'app.notifiable.event.payload')] private iterable $providers)
    {
    }

    public function getPayloadData(NotifiableEventInterface $notifiableEvent): array
    {
        $notifiableEventClassName = $this->getShortClassName($notifiableEvent::class);
        foreach ($this->providers as $provider) {
            if ($provider->supports($notifiableEventClassName)) {
                return $provider->provide($notifiableEvent);
            }
        }

        throw new \RuntimeException("No event payload provider found for: $notifiableEventClassName");
    }
}