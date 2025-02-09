<?php

declare(strict_types = 1);

namespace App\Module\Note\Presentation\API;

use App\Module\Note\Domain\Action\CreateNoteAction;
use App\Module\Note\Domain\DTO\CreateDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Contracts\Translation\TranslatorInterface;
use Exception;

#[Route('/api/notes', name: 'api.notes.')]
class CreateNoteController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateDTO $createDTO, CreateNoteAction $createRoleAction): JsonResponse
    {
        try {
            $createRoleAction->execute($createDTO);

            return new JsonResponse(
                ['message' => $this->translator->trans('note.add.success')],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('note.add.error'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}