<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\application\queryHandler;

use App\Module\Company\Application\Event\Role\RoleViewedEvent;
use App\Module\Company\Application\Query\Role\GetRoleByUUIDQuery;
use App\Module\Company\Application\QueryHandler\Role\GetRoleByUUIDQueryHandler;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GetRoleByUUIDQueryHandlerTest extends TestCase
{
    public function testInvokeCallsRepositoryDispatchesEventAndReturnsTransformedData(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $query = new GetRoleByUUIDQuery($uuid);

        $roleMock = $this->createMock(Role::class);
        $roleReader = $this->createMock(RoleReaderInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $roleReader
            ->expects($this->once())
            ->method('getRoleByUUID')
            ->with($uuid)
            ->willReturn($roleMock);

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(
                fn (RoleViewedEvent $event) =>
                    $event->getData()[GetRoleByUUIDQuery::ROLE_UUID] === $uuid
            ));

        $validators = [];

        $handler = new GetRoleByUUIDQueryHandler(
            $roleReader,
            $eventDispatcher,
            $validators
        );

        $result = $handler($query);

        $this->assertIsArray($result);
    }
}
