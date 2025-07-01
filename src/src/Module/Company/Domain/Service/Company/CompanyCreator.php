<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
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

    public function create(CompanyCreatedEvent $event): void
    {
        $this->company = new Company();
        $this->setCompany($event);

        $this->companyWriterRepository->saveCompanyInDB($this->company);
    }

    protected function setCompany(CompanyCreatedEvent $event): void
    {
        $this->setCompanyMainData($event);
        $this->setAddress($event->address);
        $this->setContacts($event->phones, $event->emails, $event->websites);
        $this->setCompanyRelations($event);
    }

    protected function setCompanyMainData(CompanyCreatedEvent $event): void
    {
        $this->company->setUUID($event->uuid->toString());
        $this->company->setFullName($event->fullName);
        $this->company->setShortName($event->shortName);
        $this->company->setNIP($event->nip->getValue());
        $this->company->setREGON($event->regon->getValue());
        $this->company->setDescription($event->description);
        $this->company->setActive($event->active);
    }

    protected function setCompanyRelations(CompanyCreatedEvent $event): void
    {
        if (null !== $event->industryUUID) {
            $industry = $this->industryReaderRepository->getIndustryByUUID($event->industryUUID->toString());
            if ($industry instanceof Industry) {
                $this->company->setIndustry($industry);
            }
        }

        if (null !== $event->parentCompanyUUID) {
            $parentCompany = $this->companyReaderRepository->getCompanyByUUID($event->parentCompanyUUID->toString());
            if ($parentCompany instanceof Company) {
                $this->company->setParentCompany($parentCompany);
            }
        }

        foreach ($this->contacts as $contact) {
            $this->company->addContact($contact);
        }

        $this->company->setAddress($this->address);
    }

    protected function setContacts(Phones $phones, ?Emails $emails = null, ?Websites $websites = null): void
    {
        $dataSets = [
            ContactTypeEnum::PHONE->value => $phones->toArray(),
            ContactTypeEnum::EMAIL->value => $emails->toArray(),
            ContactTypeEnum::WEBSITE->value => $websites->toArray(),
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

    protected function setAddress(AddressValueObject $address): void
    {
        $this->address->setStreet($address->getStreet());
        $this->address->setPostcode($address->getPostcode());
        $this->address->setCity($address->getCity());
        $this->address->setCountry($address->getCountry());
        $this->address->setActive($address->getActive());
    }
}
