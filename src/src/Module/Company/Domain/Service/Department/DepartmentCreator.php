<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

class DepartmentCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected Company $company,
        protected Department $department,
        protected ?Department $parentDepartment,
        protected Address $address,
        protected CompanyReaderInterface $companyReaderRepository,
        protected DepartmentReaderInterface $departmentReaderRepository,
        protected DepartmentWriterInterface $departmentWriterRepository,
   ) {
        $this->contacts = new ArrayCollection();
    }

    public function create(CreateDepartmentCommand $command): void
    {
        $this->setDepartment($command);
        $this->departmentWriterRepository->saveDepartmentInDB($this->department);
    }

    protected function setDepartment(CommandInterface $command): void
    {
        $this->setCompany($command->companyUUID);
        $this->setParentDepartment($command->parentDepartmentUUID);
        $this->setAddress($command->address);
        $this->setContacts($command->phones, $command->emails, $command->websites);

        $this->setDepartmentMainData($command);
        $this->setDepartmentRelations();
    }

    protected function setDepartmentMainData(CommandInterface $command): void
    {
        $this->department->setName($command->name);
        $this->department->setDescription($command->description);
        $this->department->setActive($command->active);
    }

    protected function setDepartmentRelations(): void
    {
        $this->department->setCompany($this->company);

        if (null !== $this->parentDepartment) {
            $this->department->setParentDepartment($this->parentDepartment);
        }

        foreach ($this->contacts as $contact) {
            $this->department->addContact($contact);
        }

        $this->department->setAddress($this->address);
    }

    protected function setCompany(?string $companyUUID): void
    {
        $this->company = $this->companyReaderRepository->getCompanyByUUID($companyUUID);
    }

    protected function setParentDepartment(?string $parentDepartmentUUID): void
    {
        if (null === $parentDepartmentUUID) {
            $this->parentDepartment = null;

            return;
        }

        $this->parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($parentDepartmentUUID);
    }

    protected function setContacts(array $phones, array $emails = [], array $websites = []): void
    {
        $dataSets = [
            ContactTypeEnum::PHONE->value => $phones,
            ContactTypeEnum::EMAIL->value => $emails,
            ContactTypeEnum::WEBSITE->value => $websites,
        ];

        foreach ($dataSets as $type => $values) {
            foreach ($values as $value) {
                $contact = new Contact();
                $contact->setType($type);
                $contact->setData($value);

                $this->contacts[] = $contact;
            }
        }
    }

    protected function setAddress(AddressDTO $addressDTO): void
    {
        $this->address->setStreet($addressDTO->street);
        $this->address->setPostcode($addressDTO->postcode);
        $this->address->setCity($addressDTO->city);
        $this->address->setCountry($addressDTO->country);
        $this->address->setActive($addressDTO->active);
    }
}