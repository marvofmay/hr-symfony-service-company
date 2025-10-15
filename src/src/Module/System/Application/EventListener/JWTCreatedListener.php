<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        $payload['employeeUUID'] = $user->getEmployee()?->getUUID();
        $payload['email'] = $user->getEmail();
        $payload['roles'] = $user->getRoles();
        //$payload['modules'] = ;
        //$payload['accesses'] = ;
        //$payload['permissions'] = ;

        $event->setData($payload);
    }
}