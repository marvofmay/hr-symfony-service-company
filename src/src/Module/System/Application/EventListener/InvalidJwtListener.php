<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_invalid', method: 'onJWTInvalid')]
final readonly class InvalidJwtListener
{
    public function __construct(private TranslatorInterface $translator) {}

    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $response = new JsonResponse([
            'code'    => Response::HTTP_UNAUTHORIZED,
            'message' => $this->translator->trans('token.invalid', [], 'security'),
        ], Response::HTTP_UNAUTHORIZED);

        $event->setResponse($response);
    }
}