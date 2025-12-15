<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Transformer\Department;

use App\Common\Domain\Interface\DataTransformerInterface;
use App\Module\Company\Application\QueryHandler\Department\ListDepartmentsQueryHandler;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use Doctrine\Common\Collections\Collection;

class DepartmentDataTransformer implements DataTransformerInterface
{
    public static function supports(): string
    {
        return ListDepartmentsQueryHandler::class;
    }

    public function transformToArray(Department $department, array $includes = []): array
    {
        $data = [
            Department::COLUMN_UUID => $department->getUUID()->toString(),
            Department::COLUMN_NAME => $department->getName(),
            Department::COLUMN_INTERNAL_CODE => $department->getInternalCode(),
            Department::COLUMN_DESCRIPTION => $department->getDescription(),
            Department::COLUMN_ACTIVE => $department->getActive(),
            Department::COLUMN_CREATED_AT => $department->createdAt?->format('Y-m-d H:i:s'),
            Department::COLUMN_UPDATED_AT => $department->getUpdatedAt()?->format('Y-m-d H:i:s'),
            Department::COLUMN_DELETED_AT => $department->getDeletedAt()?->format('Y-m-d H:i:s'),
        ];

        foreach ($includes as $relation) {
            if (in_array($relation, Department::getRelations(), true)) {
                $data[$relation] = $this->transformRelation($department, $relation);
            }
        }

        return $data;
    }

    private function transformRelation(Department $department, string $relation): ?array
    {
        return match ($relation) {
            Department::RELATION_EMPLOYEES => $this->transformEmployees($department->getEmployees()),
            Department::RELATION_PARENT_DEPARTMENT => $this->transformParentDepartment($department->getParentDepartment()),
            Department::RELATION_COMPANY => $this->transformCompany($department->getCompany()),
            Department::RELATION_ADDRESS => $this->transformAddress($department->getAddress()),
            Department::RELATION_CONTACTS => $this->transformContacts($department->getContacts()),
            default => null,
        };
    }

    private function transformEmployees(?Collection $employees): ?array
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

    private function transformParentDepartment(?Department $parentDepartment): ?array
    {
        if (!$parentDepartment) {
            return null;
        }

        return [
            Department::COLUMN_UUID => $parentDepartment->getUUID()->toString(),
            Department::COLUMN_NAME => $parentDepartment->getName(),
        ];
    }

    private function transformCompany(Company $company): ?array
    {
        return [
            Company::COLUMN_UUID => $company->getUUID()->toString(),
            Company::COLUMN_FULL_NAME => $company->getFullName(),
            Company::COLUMN_SHORT_NAME => $company->getShortName(),
            Company::COLUMN_NIP => $company->getNIP(),
            Company::COLUMN_REGON => $company->getRegon(),
            Company::COLUMN_INTERNAL_CODE => $company->getInternalCode(),
            Company::COLUMN_DESCRIPTION => $company->getDescription(),
            Company::COLUMN_ACTIVE => $company->getActive(),
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
