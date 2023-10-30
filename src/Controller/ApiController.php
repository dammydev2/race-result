<?php

namespace App\Controller;

use App\Actions\CreateAction;
use App\Actions\FetchRacesAction;
use App\Actions\RaceResultsAction;
use App\Actions\UpdateAction;
use App\Validator\RaceValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class ApiController extends AbstractController
{
    private $raceValidator;
    private $createAction;
    private $fetchRacesAction;
    private $raceResultsAction;
    private $updateAction;

    public function __construct(
        RaceValidator $raceValidator,
        CreateAction $createAction,
        FetchRacesAction $fetchRacesAction,
        RaceResultsAction $raceResultsAction,
        UpdateAction $updateAction
    ) {
        $this->raceValidator = $raceValidator;
        $this->createAction = $createAction;
        $this->fetchRacesAction = $fetchRacesAction;
        $this->raceResultsAction = $raceResultsAction;
        $this->updateAction = $updateAction;
    }

    #[Route('/api/imported-races', methods: ['POST', 'HEAD'])]
    public function importResults(Request $request): Response
    {
        
        $errors = $this->raceValidator->validate($request->request->all(), $request->files->get('file'));

        if (count($errors) > 0) {
            $jsonResponse = json_encode(['errors' => $errors]);
            return new Response($jsonResponse, Response::HTTP_BAD_REQUEST, ['content-type' => 'application/json']);
        }
        
        return $this->createAction->handle($request);
    }

    #[Route('/api/imported-races', methods: ['GET', 'HEAD'])]
    public function getImportedRaces(Request $request): JsonResponse
    {
        return $this->fetchRacesAction->handle($request);
    }

    #[Route('/api/race/{raceId}/results', methods: ['GET', 'HEAD'])]
    public function getRaceResults(int $raceId): JsonResponse
    {
        return $this->raceResultsAction->handle($raceId);
    }

    #[Route('/api/race/results/{raceId}', methods: ['PATCH', 'HEAD'])]
    public function editRaceResult(int $raceId, Request $request): Response
    {
        return $this->updateAction->handle($raceId, $request);
    }
}