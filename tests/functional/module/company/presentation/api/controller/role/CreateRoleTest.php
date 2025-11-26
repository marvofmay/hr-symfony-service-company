<?php

namespace App\tests\functional\module\company\presentation\api\controller\role;

use App\tests\functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Response;

class CreateRoleTest extends FunctionalTestBase
{
    public function testCreateRole(): void
    {
        $this->getAuthenticatedClient();

        $this->client->request('POST', '/api/roles', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Some role',
            'description' => 'Some role description ...',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
