<?php

declare(strict_types=1);

namespace App\Module\System\Application\CommandHandler\Auth;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\System\Application\Command\Auth\LogoutCommand;
use App\Module\System\Application\Event\Auth\UserLogoutEvent;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\Service\AuthEvent\AuthEventRecorder;
use App\Module\System\Domain\Service\AuthEvent\BlacklistTokenService;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class LogoutCommandHandler
{
    public function __construct(
        private BlacklistTokenService $blacklist,
        private AuthEventRecorder $authEventRecorder,
        private MessageService $messageService,
        private TokenStorageInterface $tokenStorage,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(LogoutCommand $command): void
    {
        $authHeader = $command->request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new \Exception($this->messageService->get('token.param.missing', [], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $jwt = substr($authHeader, 7);
        $payload = json_decode(base64_decode(explode('.', $jwt)[1]), true);

        if (!isset($payload['tokenUUID'])) {
            throw new \Exception($this->messageService->get('tokenUUID.param.missing', [], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user instanceof UserInterface) {
            throw new \Exception($this->messageService->get('user.notFound', [], 'security'), Response::HTTP_BAD_REQUEST);
        }

        $tokenUUID = TokenUUID::fromString($payload['tokenUUID']);
        $expiresAt = isset($payload['exp']) ? new \DateTime()->setTimestamp((int)$payload['exp']) : null;

        if ($this->blacklist->isRevoked($tokenUUID)) {
            throw new \Exception($this->messageService->get('user.logout.alreadyExists', [], 'security'), Response::HTTP_BAD_REQUEST);
        }
        $this->blacklist->revoke(user: $user, tokenUUID: $tokenUUID, expiresAt: $expiresAt);
        $this->authEventRecorder->record(user: $user, type: AuthEventTypeEnum::LOGOUT, tokenUUID: $tokenUUID);

        $this->eventDispatcher->dispatch(new UserLogoutEvent([
            LogoutCommand::TOKEN_UUID => $tokenUUID,
        ]));
    }
}