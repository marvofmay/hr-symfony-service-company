<?php

namespace App\tests\unit\module\company\domain\dto\role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateRoleAccessPermissionTest extends KernelTestCase
{
    private ValidatorInterface $validator;
    private MessageService $messageService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->validator = $container->get(ValidatorInterface::class);
        $this->messageService = $container->get(MessageService::class);
    }

    public function testNotExistsAccessAndPermissionsValidation(): void
    {
        $dto = new CreateAccessPermissionDTO();
        $dto->accesses = [
            [
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'permissions' => [
                    '123e4567-e89b-12d3-a456-426614174000',
                    '9bfa1a96-25f1-4f6a-a5d9-4bd3f7c84d35',
                ],
            ],
        ];

        $violations = $this->validator->validate($dto);
        $this->assertCount(3, $violations);
    }

    public function testAccessesCannotBeBlank(): void
    {
        $dto = new CreateAccessPermissionDTO();
        $dto->accesses = [];

        $violations = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($violations));

        $this->assertSame('role.accesses.required', $violations[0]->getMessage());
    }

    private function violationContainsMessage(iterable $violations, string $message): bool
    {
        foreach ($violations as $violation) {
            if (str_contains($violation->getMessage(), $message)) {
                return true;
            }
        }

        return false;
    }
}
