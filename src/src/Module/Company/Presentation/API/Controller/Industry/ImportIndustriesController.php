<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Industry;

use App\Module\Company\Application\Facade\ImportIndustriesFacade;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportIndustriesController extends AbstractController
{
    public function __construct(
        private readonly ImportIndustriesFacade $importIndustriesFacade,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/industries/import', name: 'import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::INDUSTRY)) {
            throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
        }

        if (!$file) {
            return new JsonResponse(['message' => $this->translator->trans('industry.import.file.required', [], 'industries')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->importIndustriesFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
