<?php

declare(strict_types=1);

namespace App\Module\Company\Structure;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PayloadErrorEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $isHttpEvent = $event->getThrowable() instanceof HttpExceptionInterface;
        $isValidationEvent = $event->getThrowable()->getPrevious() instanceof ValidationFailedException;

        if (!$isHttpEvent || !$isValidationEvent) {
            return;
        }

        $validationException = $event->getThrowable()->getPrevious();
        $errorMessages = [];
        foreach ($validationException->getViolations() as $violation) {
            $errorMessages[$this->translator->trans($violation->getPropertyPath())] = $this->translator->trans($violation->getMessage());
        }

        $event->setResponse(new JsonResponse(['errors' => $errorMessages]));
    }
}
