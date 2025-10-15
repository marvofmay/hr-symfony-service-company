<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_expired', method: 'onJWTExpired')]
readonly class ExpiredJwtListener
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $response = new JsonResponse([
            'code' => 401,
            'message' => $this->translator->trans('token.expired', [], 'security'),
        ], 401);

        $event->setResponse($response);
    }
}