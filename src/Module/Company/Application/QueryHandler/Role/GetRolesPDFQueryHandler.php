<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\Role;

use App\Module\Company\Application\Event\Role\RolesPDFCreatedEvent;
use App\Module\Company\Application\Query\Role\GetRolesPDFQuery;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\System\Domain\Service\Pdf\PDFService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetRolesPDFQueryHandler
{
    public function __construct(
        private PDFService $pdfService,
        private RoleReaderInterface $roleReaderRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(GetRolesPDFQuery $query): string
    {
        $rolesUUIDs = $query->rolesUUIDs;
        $roles = $this->roleReaderRepository->getRolesByUUIDs($rolesUUIDs);

        $this->eventDispatcher->dispatch(new RolesPDFCreatedEvent([
            GetRolesPDFQuery::ROLES_UUIDS => $rolesUUIDs,
        ]));

        return $this->pdfService->streamPdf('pdfs/role/roles_list.html.twig', ['roles' => $roles, 'baseUrl' => $_ENV['APP_URL']]);
    }
}
