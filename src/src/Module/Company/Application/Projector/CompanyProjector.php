<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Projector;

use App\Module\Company\Domain\Event\Company\CompanyCreatedEvent;
use App\Module\Company\Domain\Service\Company\CompanyCreator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class CompanyProjector
{
    public function __construct(private CompanyCreator $companyCreator,)
    {
    }


    public function onCompanyCreated(CompanyCreatedEvent $event): void
    {
        $this->companyCreator->create($event);
    }
}