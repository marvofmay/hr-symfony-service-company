<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_invalid', method: 'onJWTInvalid')]
final class InvalidJwtListener
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $event->setResponse(new JsonResponse([
            'code' => 401,
            'message' => $this->translator->trans('token.invalid', [], 'security'),
        ], 401));
    }
}
