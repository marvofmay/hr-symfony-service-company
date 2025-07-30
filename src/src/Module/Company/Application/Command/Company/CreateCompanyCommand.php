<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;

final readonly class CreateCompanyCommand implements CommandInterface
{
    public function __construct(
        public string $fullName,
        public ?string $shortName,
        public bool $active,
        public ?string $parentCompanyUUID,
        public string $nip,
        public string $regon,
        public ?string $description,
        public string $industryUUID,
        public ?array $phones,
        public ?array $emails,
        public ?array $websites,
        public AddressDTO $address,
    ) {
    }
}
