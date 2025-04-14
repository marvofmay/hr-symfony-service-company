<?php

declare(strict_types=1);

namespace App\Module\Note\Presentation\API\Controller;

use App\Module\Note\Domain\DTO\NotesQueryDTO;
use App\Module\Note\Presentation\API\Action\AskNotesAction;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListNotesController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator,) 
    {}

    #[Route('/api/notes', name: 'api.notes.list', methods: ['GET'])]
    public function list(#[MapQueryString] NotesQueryDTO $queryDTO, AskNotesAction $askNotesAction): Response
    {
        try {
            return new JsonResponse(['data' => $askNotesAction->ask($queryDTO)], Response::HTTP_OK);
        } catch (\Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('note.list.error', [], 'notes'), $error->getMessage())
            );

            return new JsonResponse(
                [
                    'data' => [],
                    'message' => $this->translator->trans('note.list.error', [], 'notes'),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
