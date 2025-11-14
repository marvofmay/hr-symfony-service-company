<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\DI;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NotifiableEventsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $listenerServiceId = 'App\Module\System\Application\EventSubscriber\GenericEventNotifierSubscriber';
        if (!$container->hasDefinition($listenerServiceId) && !$container->hasAlias($listenerServiceId)) {
            return;
        }

        $tagged = $container->findTaggedServiceIds('app.notifiable.event');
        if (empty($tagged)) {
            return;
        }

        $definition = $container->hasDefinition($listenerServiceId)
            ? $container->getDefinition($listenerServiceId)
            : $container->getAlias($listenerServiceId);

        foreach ($tagged as $eventServiceId => $tags) {
            $eventClass = $eventServiceId;
            $definition->addTag('kernel.event_listener', [
                'event'  => $eventClass,
                'method' => 'onNotifiableEvent',
            ]);
        }
    }
}