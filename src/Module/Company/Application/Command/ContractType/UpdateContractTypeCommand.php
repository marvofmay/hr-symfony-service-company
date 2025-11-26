<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\ContractType;

use App\Common\Domain\Interface\CommandInterface;

final readonly class UpdateContractTypeCommand implements CommandInterface
{
    public const string CONTRACT_TYPE_UUID        = 'contractTypeUUID';
    public const string CONTRACT_TYPE_NAME        = 'contractTypeName';
    public const string CONTRACT_TYPE_DESCRIPTION = 'contractTypeDescription';
    public const string CONTRACT_TYPE_ACTIVE      = 'contractTypeActive';

    public function __construct(
        public string $contractTypeUUID,
        public string $name,
        public ?string $description,
        public ?bool $active,
    ) {
    }
}
