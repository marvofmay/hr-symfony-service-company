<?php

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Company\Application\Query\Department\ListDepartmentsQuery;
use App\Module\Company\Domain\DTO\Department\DepartmentsQueryDTO;
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

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class ListDepartmentsController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/departments', name: 'api.departments.list', methods: ['GET'])]
    public function __invoke(#[MapQueryString] DepartmentsQueryDTO $queryDTO): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::LIST, AccessEnum::DEPARTMENTS, $this->messageService->get('accessDenied'));

        try {
            $stamp = $this->queryBus->dispatch(new ListDepartmentsQuery($queryDTO));
            $data = $stamp->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }
}
