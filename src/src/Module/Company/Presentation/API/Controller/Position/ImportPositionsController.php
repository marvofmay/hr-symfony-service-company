<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Position;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Facade\ImportPositionsFacade;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

final class ImportPositionsController extends AbstractController
{
    public function __construct(private readonly ImportPositionsFacade $importPositionsFacade, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/positions/import', name: 'import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::POSITION)) {
            throw new \Exception($this->messageService->get('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
        }

        if (!$file) {
            return new JsonResponse(['message' => $this->messageService->get('position.import.file.required', [], 'positions')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->importPositionsFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
