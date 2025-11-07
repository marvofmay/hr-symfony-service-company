<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use App\Module\System\Application\Event\Auth\UserLoginEvent;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\Service\AuthEvent\AuthEventRecorder;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
final readonly class JWTCreatedListener
{
    public function __construct(
        private AuthEventRecorder $authEventRecorder,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $email = $user->getEmail();
        $userUUID = $user->getUUID();
        $employeeUUID = $user->getEmployee()?->getUUID();
        $firstName = $user->getEmployee()?->getFirstName();

        $payload['userUUID'] = $userUUID;
        $payload['email'] = $email;
        $payload['employeeUUID'] = $employeeUUID;
        $payload['firstName'] = $firstName;
        $payload['roles'] = $user->getRoles();
        // $payload['modules'] = ;
        // $payload['accesses'] = ;
        // $payload['permissions'] = ;

        $tokenUUID = TokenUUID::generate();
        $payload['tokenUUID'] = $tokenUUID->toString();

        $event->setData($payload);

        $this->authEventRecorder->record(user: $user, type: AuthEventTypeEnum::LOGIN, tokenUUID: $tokenUUID);

        $this->eventDispatcher->dispatch(new UserLoginEvent([
            'tokenUUID' => $tokenUUID,
            'userUUID' => $userUUID,
            'email' => $email,
            'employeeUUID' => $employeeUUID,
            'firstName' => $firstName,
        ]));
    }
}
