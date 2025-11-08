<?php

declare(strict_types=1);

namespace App\Module\Note\Infrastructure\DI;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LoggableEventsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $listenerServiceId = 'App\Module\System\Application\EventSubscriber\GenericEventLoggerSubscriber';
        if (!$container->hasDefinition($listenerServiceId) && !$container->hasAlias($listenerServiceId)) {
            return;
        }

        $tagged = $container->findTaggedServiceIds('app.loggable.event');
        if (empty($tagged)) {
            return;
        }

        $definition = $container->hasDefinition($listenerServiceId)
            ? $container->getDefinition($listenerServiceId)
            : $container->getAlias($listenerServiceId);

        // dla każdego odnalezionego serwisu -> dodajemy tag kernel.event_listener
        foreach ($tagged as $eventServiceId => $tags) {
            // tutaj $eventServiceId to id serwisu eventu; chcemy klasę eventu = $eventServiceId
            // ale findTaggedServiceIds zwraca identyfikatory usług; jeśli eventy są rejestrowane jako klasa -> id == klasa
            $eventClass = $eventServiceId;

            // do listenera dodajemy tag event listener dla zdarzenia $eventClass
            $definition->addTag('kernel.event_listener', [
                'event'  => $eventClass,
                'method' => 'onLoggableEvent',
            ]);
        }
    }
}