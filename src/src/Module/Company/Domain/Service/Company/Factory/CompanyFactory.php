<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company\Factory;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Company;

class CompanyFactory
{
    public function createFromEvent(DomainEventInterface $event): Company
    {
        $company = new Company();
        $company->setUUID($event->uuid->toString());
        $company->setFullName($event->fullName->getValue());
        $company->setShortName($event->shortName->getValue());
        $company->setInternalCode($event->internalCode);
        $company->setNIP($event->nip->getValue());
        $company->setREGON($event->regon->getValue());
        $company->setDescription($event->description);
        $company->setActive($event->active);

        return $company;
    }

    public function updateFromEvent(Company $company, DomainEventInterface $event): void
    {
        $company->setFullName($event->fullName->getValue());
        $company->setShortName($event->shortName->getValue());
        $company->setNIP($event->nip->getValue());
        $company->setREGON($event->regon->getValue());
        $company->setDescription($event->description);
        $company->setActive($event->active);
    }
}