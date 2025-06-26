<?php

declare(strict_types=1);

namespace App\tests\functional;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Response;

abstract class FunctionalTestBase extends WebTestCase
{
    protected EntityManagerInterface $em;
    protected KernelBrowser $client;
    protected MessageService $messageService;

    protected function setUp(): void
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->messageService = static::getContainer()->get(MessageService::class);

        $this->resetDatabase();
        $this->initializeData();
    }

    private function resetDatabase(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        if (!empty($metadata)) {
            $schemaTool->createSchema($metadata);
        }
    }

    private function initializeData(): void
    {
        $application = new Application(static::$kernel);
        $command = $application->find('app:initialize-system-default-data');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }

    protected function getAuthenticatedClient(string $email = 'admin.hrapp@gmail.com', string $password = 'Admin123!'): KernelBrowser
    {
        $users = $this->em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            echo 'user email: ' . $user->getEmail() . ' --- user hashed password: ' . $user->getPassword() . PHP_EOL;
        }

        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $email,
            'password' => $password,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        $token = $data['token'] ?? null;

        if (!$response->isSuccessful() || !$token) {
            throw new \RuntimeException('Nie udało się zalogować użytkownika testowego.');
        }

        $this->client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);

        return $this->client;
    }
}