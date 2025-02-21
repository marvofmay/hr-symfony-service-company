<?php

declare(strict_types=1);

namespace App\tests\unit\module\company\dto\role;

use App\Module\Company\Domain\DTO\Role\CreateDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CreateDTOTest extends TestCase
{
    private CreateDTO $dto;

    protected function setUp(): void
    {
        $this->dto = new CreateDTO();
    }
}
