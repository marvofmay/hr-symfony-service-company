<?php

declare(strict_types=1);

namespace App\Module\System\Application\EventListener;

use App\Module\System\Application\Event\Auth\UserLoginEvent;
use App\Module\System\Domain\Enum\Auth\AuthEventTypeEnum;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use App\Module\System\Domain\Interface\Module\ModuleReaderInterface;
use App\Module\System\Domain\Interface\RoleAccessPermission\RoleAccessPermissionReaderInterface;
use App\Module\System\Domain\Service\AuthEvent\AuthEventRecorder;
use App\Module\System\Domain\ValueObject\TokenUUID;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
final readonly class JWTCreatedListener
{
    public function __construct(
        private AuthEventRecorder $authEventRecorder,
        private EventDispatcherInterface $eventDispatcher,
        private ModuleReaderInterface $moduleReaderRepository,
        private AccessReaderInterface $accessReaderRepository,
        private RoleAccessPermissionReaderInterface $roleAccessPermissionReaderRepository,
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        $email = $user->getEmail();
        $userUUID = $user->getUUID();
        $employeeUUID = $user->getEmployee()?->getUUID();
        $firstName = $user->getEmployee()?->getFirstName();
        $lastName = $user->getEmployee()?->getLastName();

        $payload = array_merge($payload, [
            'user'        => [
                'uuid'  => $userUUID,
                'email' => $email,
            ],
            'employee'    => [
                'uuid'      => $employeeUUID,
                'firstName' => $firstName,
                'lastName'  => $lastName,
            ],
            'roles'       => $user->getRoles(),
            'modules'     => $this->getModules($user),
            'accesses'    => $this->getAccesses($user),
            'permissions' => $this->getPermissions($user),
        ]);

        $tokenUUID = TokenUUID::generate();
        $payload['tokenUUID'] = $tokenUUID->toString();

        $event->setData($payload);

        $this->authEventRecorder->record(
            user: $user,
            type: AuthEventTypeEnum::LOGIN,
            tokenUUID: $tokenUUID
        );

        $this->eventDispatcher->dispatch(new UserLoginEvent([
            'tokenUUID'    => $tokenUUID,
            'userUUID'     => $userUUID,
            'email'        => $email,
            'employeeUUID' => $employeeUUID,
            'firstName'    => $firstName,
        ]));
    }

    private function getModules(UserInterface $user): array
    {
        $employee = $user->getEmployee();

        if (null === $employee) {
            return $this->moduleReaderRepository
                ->getModules()
                ->map(fn ($module) => $module->getName())
                ->toArray();
        }

        $role = $employee->getRole();

        return array_unique(
            $role->getAccesses()
                ->map(fn ($access) => $access->getModule()->getName())
                ->toArray()
        );
    }

    private function getAccesses(UserInterface $user): array
    {
        $employee = $user->getEmployee();

        if (null === $employee) {
            return $this->accessReaderRepository
                ->getAccesses()
                ->map(fn ($access) => $access->getName())
                ->toArray();
        }

        $role = $employee->getRole();

        return $role->getAccesses()
            ->map(fn ($access) => $access->getName())
            ->toArray();
    }

    private function getPermissions(UserInterface $user): array
    {
        $employee = $user->getEmployee();

        if (null === $employee) {
            return $this->accessReaderRepository
                ->getAccesses()
                ->map(fn ($access) => $access->getName() . '.*')
                ->toArray();
        }

        $role = $employee->getRole();

        return $this->roleAccessPermissionReaderRepository->getRoleAccessAndPermission($role)
            ->map(fn ($roleAccessPermission) => $roleAccessPermission->getAccess()->getName() . '.' . $roleAccessPermission->getPermission()->getName())
            ->toArray();
    }

}
