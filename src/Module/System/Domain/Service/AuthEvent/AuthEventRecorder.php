<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\AuthEvent;

use App\Module\System\Domain\Entity\AuthEvent;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\Interface\AuthEvent\AuthEventWriterInterface;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class AuthEventRecorder
{
    public function __construct(
        private AuthEventWriterInterface $authEventWriterRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function record(UserInterface $user, AuthEventTypeEnum $type, ?TokenUUID $tokenUUID = null, ?array $meta = null): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request?->getClientIp();
        $userAgent = $request?->headers->get('User-Agent');

        $event = AuthEvent::create(
            $user,
            $type,
            $ip,
            $userAgent,
            $tokenUUID,
            $meta
        );

        $this->authEventWriterRepository->saveAuthEventInDB($event);
    }
}
