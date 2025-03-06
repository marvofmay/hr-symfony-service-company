<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Address;

use App\Module\Company\Domain\Entity\Address;

interface AddressWriterInterface
{
    public function deleteAddressInDB(Address $address): void;
}
