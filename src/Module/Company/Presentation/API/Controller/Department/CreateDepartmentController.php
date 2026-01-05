<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Department;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\DTO\Department\CreateDTO;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[ErrorChannel(MonologChanelEnum::EVENT_STORE)]
final class CreateDepartmentController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/departments', name: 'api.departments.create', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] CreateDTO $createDTO): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionEnum::CREATE, AccessEnum::DEPARTMENTS, $this->messageService->get('accessDenied'));

        try {
            $this->commandBus->dispatch(new CreateDepartmentCommand(
                $createDTO->name,
                $createDTO->internalCode,
                $createDTO->description,
                $createDTO->active,
                $createDTO->companyUUID,
                $createDTO->parentDepartmentUUID,
                $createDTO->phones,
                $createDTO->emails,
                $createDTO->websites,
                $createDTO->address
            ));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return new JsonResponse(
            ['message' => $this->messageService->get('department.add.success', [], 'departments')],
            Response::HTTP_CREATED
        );
    }
}
