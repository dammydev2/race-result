<?php

namespace App\Actions;

use App\Entity\Race;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class RaceResultsAction{

    use ResponseTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function handle(int $raceId) : JsonResponse {
        $race = $this->entityManager->getRepository(Race::class)->find($raceId);

        if (!$race) {
            return new JsonResponse(['error' => 'Race not found'], 404);
        }

        $raceResults = [
            'Racer full name' => $race->getFullName(),
            'Finish time' => $race->getFinishTIme(),
            'Distance' => $race->getDistance(),
            'Age category' => $race->getAgeCategory(),
            'Overall place' => $race->getOverallPlacement(),
            'Age category place' => $race->getAgeCategoryPlacement()
        ];

        return $this->successResponse('Races fetched successfully', $raceResults);

    }

}