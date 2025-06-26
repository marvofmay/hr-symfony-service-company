<?php

namespace App\tests\functional\api\role;

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
            'name' => 'Rola xxx',
            'description' => 'opis roli xxx',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($this->client->getResponse()->getContent());
    }
}