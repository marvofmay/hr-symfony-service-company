<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Interface\User\UserReaderInterface;
use App\Module\System\Application\Event\Auth\TokenExpiredEvent;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\Service\AuthEvent\AuthEventRecorder;
use App\Module\System\Domain\Service\AuthEvent\BlacklistTokenService;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_expired', method: 'onJWTExpired')]
final readonly class ExpiredJwtListener
{
    public function __construct(
        private TranslatorInterface $translator,
        private BlacklistTokenService $blacklist,
        private AuthEventRecorder $authEventRecorder,
        private MessageService $messageService,
        private EventDispatcherInterface $eventDispatcher,
        private UserReaderInterface $userReaderRepository,
    ) {}

    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $authHeader = $event->getRequest()->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new \Exception($this->messageService->get('token.missing', [], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $jwt = substr($authHeader, 7);
        $payload = json_decode(base64_decode(explode('.', $jwt)[1]), true);

        if (!isset($payload['tokenUUID'])) {
            throw new \Exception($this->messageService->get('tokenUUID.missing', [':uuid' => $payload['tokenUUID']], 'security'), Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['userUUID'])) {
            throw new \Exception($this->messageService->get('userUUID.missing', [':uuid' => $payload['userUUID']], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userReaderRepository->getUserByUUID($payload['userUUID']);
        if (!$user instanceof UserInterface) {
            throw new \Exception($this->messageService->get('user.notFound', [], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $tokenUUID = TokenUUID::fromString($payload['tokenUUID']);
        $expiresAt = isset($payload['exp']) ? new \DateTime()->setTimestamp((int)$payload['exp']) : null;

        if ($this->blacklist->isRevoked($tokenUUID)) {
            $response = new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $this->messageService->get('user.logout.alreadyExists', [], 'security'),
            ], Response::HTTP_BAD_REQUEST);

            $event->setResponse($response);

            return;
        }

        $this->blacklist->revoke(user: $user, tokenUUID: $tokenUUID, expiresAt: $expiresAt);
        $this->authEventRecorder->record(user: $user, type: AuthEventTypeEnum::TOKEN_EXPIRED, tokenUUID: $tokenUUID);

        $this->eventDispatcher->dispatch(new TokenExpiredEvent([
            'tokenUUID' => $tokenUUID,
        ]));

        $response = new JsonResponse([
            'code'    => Response::HTTP_UNAUTHORIZED,
            'message' => $this->translator->trans('token.expired', [], 'security'),
        ], 401);

        $event->setResponse($response);
    }
}
