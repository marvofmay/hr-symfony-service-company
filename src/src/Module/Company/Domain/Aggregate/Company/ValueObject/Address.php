<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

use App\Common\Domain\DTO\AddressDTO;

final class Address
{
    private string $street;
    private string $postcode;
    private string $city;
    private string $country;
    private bool $active;

    public function __construct(string $street, string $postcode, string $city, string $country, bool $active = true)
    {
        $this->street = $street;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->country = $country;
        $this->active = $active;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public static function fromDTO(AddressDTO $dto): self
    {
        return new self(
            $dto->street,
            $dto->postcode,
            $dto->city,
            $dto->country,
        );
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country' => $this->country,
            'active' => $this->active,
        ];
    }
}