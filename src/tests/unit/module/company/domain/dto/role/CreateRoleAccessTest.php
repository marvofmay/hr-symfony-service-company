<?php

namespace App\tests\unit\module\company\domain\dto\role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Domain\DTO\Role\CreateAccessDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateRoleAccessTest extends KernelTestCase
{
    private ValidatorInterface $validator;
    private MessageService     $messageService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->validator = $container->get(ValidatorInterface::class);
        $this->messageService = $container->get(MessageService::class);
    }

    public function testAccessUUIDCannotBeBlank(): void
    {
        $dto = new CreateAccessDTO();
        $dto->accessUUID = [];

        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);

        $violation = $violations[0];

        $this->assertSame($this->messageService->get('access.uuid.required', [], 'accesses'), $violation->getMessage());
    }

    public function testAccessUUIDMustContainValidUUIDs(): void
    {
        $dto = new CreateAccessDTO();
        $dto->accessUUID = ['invalid-uuid'];

        $violations = $this->validator->validate($dto);

        $this->assertCount(1, $violations);

        $violation = $violations[0];
        $this->assertSame($this->messageService->get('uuid.invalid', [], 'validators'), $violation->getMessage());
    }

    public function testNotExistsAccessValidation(): void
    {

        $dto = new CreateAccessDTO();
        $dto->accessUUID = [
            '550e8400-e29b-41d4-a716-446655440000',
            '123e4567-e89b-12d3-a456-426614174000',
        ];

        $violations = $this->validator->validate($dto);

        $this->assertCount(2, $violations);
    }
}