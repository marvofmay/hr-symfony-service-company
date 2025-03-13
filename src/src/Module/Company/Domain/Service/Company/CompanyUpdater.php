<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

class CompanyUpdater extends CompanyCreator
{
    public function __construct(
        protected Company $company,
        protected Address $address,
        protected CompanyWriterInterface $companyWriterRepository,
        protected CompanyReaderInterface $companyReaderRepository,
        protected IndustryReaderInterface $industryReaderRepository,
        protected ContactWriterInterface $contactWriterRepository,
        protected AddressWriterInterface $addressWriterRepository,
    ) {
        parent::__construct($company, $address, $companyWriterRepository, $companyReaderRepository, $industryReaderRepository);
    }

    public function update(Company $company, CommandInterface $command): void
    {
        $this->company = $company;
        $this->setCompany($command);
        $this->setCompanyRelations($command);

        $this->companyWriterRepository->updateCompanyInDB($this->company);
    }

    protected function setContacts(array $phones, array $emails = [], array $websites = []): void
    {
        foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
            $contacts = $this->company->getContacts($enum);
            $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
        }

        parent::setContacts($phones, $emails, $websites);
    }

    protected function setAddress(AddressDTO $addressDTO): void
    {
        $address = $this->company->getAddress();
        $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);

        parent::setAddress($addressDTO);
    }
}