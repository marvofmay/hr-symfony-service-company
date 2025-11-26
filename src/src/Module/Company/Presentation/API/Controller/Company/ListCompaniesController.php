<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Company\Application\Query\Company\ListCompaniesQuery;
use App\Module\Company\Domain\DTO\Company\CompaniesQueryDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_STORE)]
final class ListCompaniesController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/companies', name: 'api.companies.list', methods: ['GET'])]
    public function __invoke(#[MapQueryString] CompaniesQueryDTO $queryDTO): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::LIST, AccessEnum::COMPANY, $this->messageService->get('accessDenied'));

        try {
            $handledStamp = $this->queryBus->dispatch(new ListCompaniesQuery($queryDTO));
            $data = $handledStamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }
}
