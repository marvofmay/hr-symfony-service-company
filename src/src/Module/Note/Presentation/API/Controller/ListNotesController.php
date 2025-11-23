<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Note\Application\Query\ListNotesQuery;
use App\Module\Note\Domain\DTO\NotesQueryDTO;
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
final class ListNotesController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'query.bus')] private readonly MessageBusInterface $queryBus,
        private readonly MessageService $messageService,
    )
    {
    }

    #[Route('/api/employees/notes', name: 'api.employees.notes.list', methods: ['GET'])]
    public function __invoke(#[MapQueryString] NotesQueryDTO $queryDTO): Response
    {
        $this->denyAccessUnlessGranted(PermissionEnum::LIST, AccessEnum::NOTE, $this->messageService->get('accessDenied'));

        try {
            $stamp = $this->queryBus->dispatch(new ListNotesQuery($queryDTO))->last(HandledStamp::class);
            $data = $stamp->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }
}