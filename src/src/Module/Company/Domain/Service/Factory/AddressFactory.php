<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Factory;

use App\Module\Company\Domain\Aggregate\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Entity\Address;

final readonly class AddressFactory
{
    public function create(AddressValueObject $addressValueObject): Address
    {
        $address = new Address();
        $address->setStreet($addressValueObject->getStreet());
        $address->setPostcode($addressValueObject->getPostcode());
        $address->setCity($addressValueObject->getCity());
        $address->setCountry($addressValueObject->getCountry());
        $address->setActive($addressValueObject->getActive());

        return $address;
    }
}
