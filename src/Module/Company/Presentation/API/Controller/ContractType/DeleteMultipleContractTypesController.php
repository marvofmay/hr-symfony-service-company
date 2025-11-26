<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Common\Domain\Enum\MonologChanelEnum;
use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Common\Infrastructure\Http\Attribute\ErrorChannel;
use App\Module\Company\Application\Command\ContractType\DeleteMultipleContractTypesCommand;
use App\Module\Company\Domain\DTO\ContractType\DeleteMultipleDTO;
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

#[ErrorChannel(MonologChanelEnum::EVENT_LOG)]
final class DeleteMultipleContractTypesController extends AbstractController
{
    public function __construct(
        #[Autowire(service: 'command.bus')] private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/contract_types/multiple', name: 'api.contract_types.delete_multiple', methods: ['DELETE'])]
    public function __invoke(#[MapRequestPayload] DeleteMultipleDTO $deleteMultipleDTO): JsonResponse
    {
        $this->denyAccessUnlessGranted(PermissionEnum::DELETE, AccessEnum::CONTRACT_TYPE, $this->messageService->get('accessDenied'));

        try {
            $this->commandBus->dispatch(new DeleteMultipleContractTypesCommand($deleteMultipleDTO->contractTypesUUIDs));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }

        return new JsonResponse(
            ['message' => $this->messageService->get('contractType.delete.multiple.success', [], 'contract_types')],
            Response::HTTP_OK
        );
    }
}
