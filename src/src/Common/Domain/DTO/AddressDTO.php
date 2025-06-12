<?php

declare(strict_types=1);

namespace App\Common\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AddressDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $street = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $postcode = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $city = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $country = null,

        #[Assert\Type(
            type: 'bool',
        )]
        public bool $active = true,
    ) {}
}