<?php

namespace App\tests\functional\module\company\presentation\api\controller\role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\System\Application\Console\DefaultData\Data\RoleEnum;
use App\tests\functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Response;

class UpdateRoleTest extends FunctionalTestBase
{
    public function testUpdateRole(): void
    {
        $this->getAuthenticatedClient();

        $container = self::getContainer();
        $repoReader = $container->get(RoleReaderInterface::class);
        $messageService = $container->get(MessageService::class);

        $role = $repoReader->getRoleByName(
            $messageService->get(
                sprintf('role.defaultData.name.%s', RoleEnum::ADMIN->value),
                [':qty' => 3],
                'roles'
            )
        );

        $this->assertInstanceOf(Role::class, $role);

        $roleUUID = $role->getUUID();

        $this->assertIsString($roleUUID->toString());

        $updatedRoleName = 'Updated role';
        $updatedRoleDescription = 'Updated role description';

        $this->client->request('PUT', '/api/roles/' . $roleUUID, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => $updatedRoleName,
            'description' => 'Updated role description',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $role = $repoReader->getRoleByUUID($roleUUID);

        $this->assertInstanceOf(Role::class, $role);

        $this->assertSame($updatedRoleName, $role->getName());
        $this->assertSame($updatedRoleDescription, $role->getDescription());
    }
}