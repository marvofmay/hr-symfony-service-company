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
        $uuid = 'some-uuid';

        $roleMock = $this->getMockBuilder(Role::class)
            ->onlyMethods(['getName', 'getDescription'])
            ->getMock();
        $roleReaderMock = $this->createMock(RoleReaderInterface::class);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $roleReaderMock->expects($this->once())
            ->method('getRoleByUUID')
            ->with($uuid)
            ->willReturn($roleMock);

        $eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn ($event) => $event instanceof RoleViewedEvent && $event->getData()[Role::COLUMN_UUID] === $uuid));

        $handler = new GetRoleByUUIDQueryHandler($roleReaderMock, $eventDispatcherMock);

        $result = $handler(new GetRoleByUUIDQuery($uuid));

        $this->assertIsArray($result);
    }
}