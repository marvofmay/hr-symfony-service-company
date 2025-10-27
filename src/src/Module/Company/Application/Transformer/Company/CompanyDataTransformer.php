<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Company;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryEntityFieldEnum;
use Doctrine\Common\Collections\Collection;

class CompanyDataTransformer
{
    public function transformToArray(Company $company, array $includes = []): array
    {
        $data = [
            Company::COLUMN_UUID => $company->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $company->getFullName(),
            Company::COLUMN_SHORT_NAME => $company->getShortName(),
            Company::COLUMN_NIP => $company->getNIP(),
            Company::COLUMN_REGON => $company->getREGON(),
            Company::COLUMN_DESCRIPTION => $company->getDescription(),
            Company::COLUMN_ACTIVE => $company->getActive(),
            Company::COLUMN_CREATED_AT => $company->createdAt?->format('Y-m-d H:i:s'),
            Company::COLUMN_UPDATED_AT => $company->updatedAt?->format('Y-m-d H:i:s'),
            Company::COLUMN_DELETED_AT => $company->deletedAt?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Company::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($company, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Company $company, string $relation): ?array
    {
        return match ($relation) {
            Company::RELATION_DEPARTMENTS => $this->transformDepartments($company->getDepartments()),
            Company::RELATION_ADDRESS => $this->transformAddress($company->getAddress()),
            Company::RELATION_CONTACTS => $this->transformContacts($company->getContacts()),
            Company::RELATION_INDUSTRY => $this->transformIndustry($company->getIndustry()),
            Company::RELATION_PARENT_COMPANY => $this->transformParentCompany($company->getParentCompany()),
            default => null,
        };
    }

    private function transformDepartments(?Collection $departments): ?array
    {
        if (null === $departments || $departments->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Department $department) => [
                Department::COLUMN_UUID => $department->getUUID()->toString(),
                Department::COLUMN_NAME => $department->getName(),
                Department::COLUMN_DESCRIPTION => $department->getDescription(),
                Department::COLUMN_ACTIVE => $department->getActive(),
            ],
            $departments->toArray()
        );
    }

    private function transformEmployees($employees): ?array
    {
        if (null === $employees || $employees->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Employee $employee) => [
                Employee::COLUMN_UUID => $employee->getUUID()->toString(),
                Employee::COLUMN_FIRST_NAME => $employee->getFirstName(),
                Employee::COLUMN_LAST_NAME => $employee->getLastName(),
                Employee::COLUMN_PESEL => $employee->getPESEL(),
            ],
            $employees->toArray()
        );
    }

    private function transformIndustry(?Industry $industry): ?array
    {
        if (!$industry) {
            return null;
        }

        return [
            IndustryEntityFieldEnum::UUID->value => $industry->getUUID()->toString(),
            IndustryEntityFieldEnum::NAME->value => $industry->getName(),
            IndustryEntityFieldEnum::DESCRIPTION->value => $industry->getDescription(),
        ];
    }

    private function transformParentCompany(?Company $parentCompany): ?array
    {
        if (!$parentCompany) {
            return null;
        }

        return [
            Company::COLUMN_UUID => $parentCompany->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $parentCompany->getFullName(),
            Company::COLUMN_SHORT_NAME => $parentCompany->getShortName(),
            Company::COLUMN_NIP => $parentCompany->getNIP(),
            Company::COLUMN_REGON => $parentCompany->getREGON(),
        ];
    }

    private function transformAddress(?Address $address): ?array
    {
        if (!$address) {
            return null;
        }

        return [
            Address::COLUMN_UUID => $address->getUUID()->toString(),
            Address::COLUMN_STREET => $address->getStreet(),
            Address::COLUMN_POSTCODE => $address->getPostcode(),
            Address::COLUMN_CITY => $address->getCity(),
            Address::COLUMN_COUNTRY => $address->getCountry(),
        ];
    }

    private function transformContacts(?Collection $contacts): ?array
    {
        if (null === $contacts || $contacts->isEmpty()) {
            return null;
        }

        return array_map(
            fn (Contact $contact) => [
                Contact::COLUMN_TYPE => $contact->getType(),
                Contact::COLUMN_DATA => $contact->getData(),
            ],
            $contacts->toArray()
        );
    }
}
