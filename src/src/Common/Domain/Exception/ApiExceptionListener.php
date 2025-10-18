<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException', priority: 255)]
final readonly class ApiExceptionListener
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = 500;
        $message = $this->messageService->get('internalServerError');

        if ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $message = $this->messageService->get('endpointNotFound');
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = $this->messageService->get('methodNotAllowed');
        }

        $response = new JsonResponse(
            ['message' => $message],
            $status
        );

        $event->setResponse($response);
    }
}