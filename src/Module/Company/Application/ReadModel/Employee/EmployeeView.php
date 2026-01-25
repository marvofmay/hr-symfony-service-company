<?php

declare(strict_types=1);

namespace App\Module\Company\Application\ReadModel\Employee;

use App\Module\Company\Application\ReadModel\Address\AddressView;
use App\Module\Company\Application\ReadModel\Contact\ContactView;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Employee;

final readonly class EmployeeView
{
    public function __construct(
        public string $uuid,
        public string $firstName,
        public string $lastName,
        public string $position,
        public string $role,
        public string $contractType,
        public ?AddressView $address,
        public array $contacts
    ) {
    }

    public static function fromEmployee(Employee $employee): self
    {
        return new self(
            uuid: $employee->getUUID()->toString(),
            firstName: $employee->getFirstName(),
            lastName: $employee->getLastName(),
            position: $employee->getPosition()->getName(),
            role: $employee->getRole()->getName(),
            contractType: $employee->getContractType()->getName(),
            address: $employee->getAddress()
                ? AddressView::fromAddress($employee->getAddress())
                : null,
            contacts: array_map(
                fn (Contact $contact) => ContactView::fromContact($contact),
                $employee->getContacts()->toArray()
            )
        );
    }
}
