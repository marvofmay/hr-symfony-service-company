<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof MethodNotAllowedHttpException) {
            $response = new JsonResponse([
                'message' => $this->translator->trans('Niepoprawna metoda HTTP dla tego zasobu. Sprawdź dokumentację API.'),
            ]);
            $event->setResponse($response);
        }

        if ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'message' => $this->translator->trans('Endpoint nie został znaleziony lub brakuje wymaganych parametrów.'),
            ]);
            $event->setResponse($response);
        }
    }
}
