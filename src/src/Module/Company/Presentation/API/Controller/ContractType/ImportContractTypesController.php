<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\ContractType;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Facade\ImportContractTypesFacade;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class ImportContractTypesController extends AbstractController
{
    public function __construct(
        private readonly ImportContractTypesFacade $importContractTypesFacade,
        private readonly MessageService $messageService,
    ) {
    }

    #[Route('/api/contract_types/import', name: 'app.contract_types.import', methods: ['POST'])]
    public function __invoke(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        $this->denyAccessUnlessGranted(
            PermissionEnum::IMPORT,
            AccessEnum::CONTRACT_TYPE,
            $this->messageService->get('accessDenied')
        );

        if (!$file) {
            return new JsonResponse(
                ['message' => $this->messageService->get('contractType.import.file.required', [], 'contract_types')],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $result = $this->importContractTypesFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
