<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Contact\Writer\ContactWriterRepository;
use Doctrine\Common\Collections\ArrayCollection;

final readonly class DepartmentMultipleCreator
{
    private Department $department;

    public function __construct(
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentWriterInterface $departmentWriterRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private ContactWriterRepository $contactWriterRepository,
        private AddressWriterInterface $addressWriterRepository,
    ) {
    }

    public function multipleCreate(array $data): void
    {
        $this->setDepartments($data);
    }

    private function setDepartments(array $data): void
    {
        $departments = new ArrayCollection();
        $temporaryDepartmentMap = [];

        foreach ($data as $item) {
            $this->setDepartment($item);
            $this->setMainDepartmentData($item);
            $this->setAddress($item);
            $this->setContacts($item);

            if (is_int($item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID])) {
                $temporaryDepartmentMap[$item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID]] = $this->department;
            }

            $departments[] = $this->department;
        }

        foreach ($data as $index => $item) {
            $department = $departments[$index];
            $this->department = $department;

            $this->setRelations($item, $temporaryDepartmentMap);
        }

        $this->departmentWriterRepository->saveDepartmentsInDB($departments);
    }

    private function setDepartment(array $item): void
    {
        if (null === $item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID] || is_int($item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID])) {
            $this->department = new Department();
        } elseif (is_string($item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID])) {
            $department = $this->departmentReaderRepository->getDepartmentByUUID($item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID]);
            if (null === $department) {
                $this->department = new Department();
            } else {
                $this->department = $department;
            }
        }
    }

    private function setMainDepartmentData(array $item): void
    {
        $this->department->setName((string) $item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_NAME]);
        $this->department->setDescription((string) $item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_DESCRIPTION]);
        $this->department->setActive((bool) $item[ImportDepartmentsFromXLSX::COLUMN_ACTIVE]);
    }

    private function setAddress(array $item): void
    {
        if (null !== $item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID] && is_string($item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID])) {
            $address = $this->department->getAddress();
            $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
        }

        $address = new Address();
        $address->setStreet($item[ImportDepartmentsFromXLSX::COLUMN_STREET]);
        $address->setPostcode($item[ImportDepartmentsFromXLSX::COLUMN_POSTCODE]);
        $address->setCity($item[ImportDepartmentsFromXLSX::COLUMN_CITY]);
        $address->setCountry($item[ImportDepartmentsFromXLSX::COLUMN_COUNTRY]);

        $this->department->setAddress($address);
    }

    private function setContacts(array $item): void
    {
        if (null !== $item[ImportDepartmentsFromXLSX::COLUMN_DEPARTMENT_UUID]) {
            foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
                $contacts = $this->department->getContacts($enum);
                $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
            }
        }

        if (null !== $item[ImportDepartmentsFromXLSX::COLUMN_PHONE]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($item[ImportDepartmentsFromXLSX::COLUMN_PHONE]);
            $contact->setActive(true);
            $this->department->addContact($contact);
        }

        if (null !== $item[ImportDepartmentsFromXLSX::COLUMN_EMAIL]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::EMAIL->value);
            $contact->setData((string) $item[ImportDepartmentsFromXLSX::COLUMN_EMAIL]);
            $contact->setActive(true);
            $this->department->addContact($contact);
        }

        if (null !== $item[ImportDepartmentsFromXLSX::COLUMN_WEBSITE]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::WEBSITE->value);
            $contact->setData($item[ImportDepartmentsFromXLSX::COLUMN_WEBSITE]);
            $contact->setActive(true);
            $this->department->addContact($contact);
        }
    }

    private function setRelations(array $item, array $temporaryDepartmentMap): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($item[ImportDepartmentsFromXLSX::COLUMN_COMPANY_UUID]);
        if ($company instanceof Company) {
            $this->department->setCompany($company);
        }

        $parentDepartmentUUID = $item[ImportDepartmentsFromXLSX::COLUMN_PARENT_DEPARTMENT_UUID] ?? null;

        if (null !== $parentDepartmentUUID) {
            if (is_int($parentDepartmentUUID) && isset($temporaryDepartmentMap[$parentDepartmentUUID])) {
                $this->department->setParentDepartment($temporaryDepartmentMap[$parentDepartmentUUID]);
            } elseif (is_string($parentDepartmentUUID)) {
                $parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($parentDepartmentUUID);
                if ($parentDepartment instanceof Department) {
                    $this->department->setParentDepartment($parentDepartment);
                }
            }
        }
    }
}
