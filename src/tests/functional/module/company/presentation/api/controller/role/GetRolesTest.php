<?php

namespace App\tests\functional\module\company\presentation\api\controller\role;

use App\Module\System\Application\Console\DefaultData\Data\RoleEnum;
use Symfony\Component\HttpFoundation\Response;

class GetRolesTest extends CreateRoleTest
{
    public function testGetRoles(): void
    {
        $this->getAuthenticatedClient();

        $this->client->request('GET', '/api/roles', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('total', $responseData['data']);
        $this->assertArrayHasKey('page', $responseData['data']);
        $this->assertArrayHasKey('limit', $responseData['data']);
        $this->assertArrayHasKey('items', $responseData['data']);
        $this->assertIsArray($responseData['data']['items']);


        $found = false;
        $translatedRoleName = $this->messageService->get(sprintf('role.defaultData.name.%s', RoleEnum::ADMIN->value), [], 'roles');
        foreach ($responseData['data']['items'] as $item) {
            if (isset($item['name']) && $item['name'] === $translatedRoleName) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, sprintf('Nie znaleziono elementu z name = "%s" w items',  $translatedRoleName));
    }
}