<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Command\Company\CreateCompanyCommand;
use App\Module\Company\Domain\DTO\Company\CreateDTO;
use App\Module\System\Application\Event\LogFileEvent;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateCompanyController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
        private readonly MessageBusInterface $commandBus,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/companies', name: 'api.companies.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(
                PermissionEnum::CREATE,
                AccessEnum::COMPANY,
                $this->messageService->get('accessDenied')
            );

            $this->dispatchCommand($createDTO);

            return $this->successResponse();
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dispatchCommand(CreateDTO $createDTO): void
    {
        try {
            $this->commandBus->dispatch(new CreateCompanyCommand(
                $createDTO->fullName,
                $createDTO->shortName,
                $createDTO->internalCode,
                $createDTO->active,
                $createDTO->parentCompanyUUID,
                $createDTO->nip,
                $createDTO->regon,
                $createDTO->description,
                $createDTO->industryUUID,
                $createDTO->phones,
                $createDTO->emails,
                $createDTO->websites,
                $createDTO->address
            ));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function successResponse(): JsonResponse
    {
        return new JsonResponse(
            ['message' => $this->messageService->get('company.add.success', [], 'companies')],
            Response::HTTP_CREATED
        );
    }

    private function errorResponse(\Exception $exception): JsonResponse
    {
        $message = sprintf(
            '%s. %s',
            $this->messageService->get('company.add.error', [], 'companies'),
            $exception->getMessage()
        );

        $this->eventBus->dispatch(new LogFileEvent($message, LogLevel::ERROR));

        $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;

        return new JsonResponse(['message' => $message], $code);
    }
}
