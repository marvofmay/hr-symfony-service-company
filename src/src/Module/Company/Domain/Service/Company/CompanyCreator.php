<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CompanyCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected Company $company,
        protected Address $address,
        protected CompanyWriterInterface $companyWriterRepository,
        protected CompanyReaderInterface $companyReaderRepository,
        protected IndustryReaderInterface $industryReaderRepository,
    ) {
        $this->contacts = new ArrayCollection();
    }

    public function create(CreateCompanyCommand $command): void
    {
        $this->company = new Company();
        $this->setCompany($command);
        $this->setCompanyRelations($command);

        $this->companyWriterRepository->saveCompanyInDB($this->company);
    }

    protected function setCompany(CommandInterface $command): void
    {
        $this->setCompanyMainData($command);
        $this->setAddress($command->address);
        $this->setContacts($command->phones, $command->emails, $command->websites);
        $this->setCompanyRelations($command);
    }

    protected function setCompanyMainData(CommandInterface $command): void
    {
        $this->company->setFullName($command->fullName);
        $this->company->setShortName($command->shortName);
        $this->company->setNIP($command->nip);
        $this->company->setREGON($command->regon);
        $this->company->setDescription($command->description);
        $this->company->setActive($command->active);
    }

    protected function setCompanyRelations(CommandInterface $command): void
    {
        if (null !== $command->industryUUID) {
            $industry = $this->industryReaderRepository->getIndustryByUUID($command->industryUUID);
            if ($industry instanceof Industry) {
                $this->company->setIndustry($industry);
            }
        }

        if (null !== $command->parentCompanyUUID) {
            $parentCompany = $this->companyReaderRepository->getCompanyByUUID($command->parentCompanyUUID);
            if ($parentCompany instanceof Company) {
                $this->company->setParentCompany($parentCompany);
            }
        }

        foreach ($this->contacts as $contact) {
            $this->company->addContact($contact);
        }

        $this->company->setAddress($this->address);
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