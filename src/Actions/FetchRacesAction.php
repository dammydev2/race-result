<?php

namespace App\Actions;

use App\Entity\Race;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class FetchRacesAction{

    use ResponseTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function handle(Request $request) : JsonResponse {
        $raceTitle = $request->query->get('race_title');
        $filtereBy = $request->query->get('filter_by');
        $filterType = $request->query->get('filter_type');

        $raceRepository = $this->entityManager->getRepository(Race::class);
        $races = $raceRepository->findAll();

        if($raceTitle){
            $races = $raceRepository->findBy(['title' => $raceTitle]);
        }
        
        if($filtereBy && $filterType){
            $races = $raceRepository->findBy([], [$filtereBy => $filterType]);

        }

        
        $formattedRaces = [];
        $totalLongDistanceTime = 0;
        $totalMediumDistanceTime = 0;
        foreach ($races as $race) {


            if ($race->getDistance() === 'long') {
                $longDistanceResults[] = $race;
                $totalLongDistanceTime += strtotime($race->getFinishTime());
            }

            $averageLongDistanceFinishTime = 0;
            if (count($longDistanceResults) > 0) {
                $averageLongDistanceFinishTime = $totalLongDistanceTime / count($longDistanceResults);
                $averageLongDistanceFinishTime = gmdate("H:i:s", $averageLongDistanceFinishTime);
            }

            $mediumDistanceResults  = [];
            if ($race->getDistance() === 'medium') {
                $mediumDistanceResults = $race;
                $totalMediumDistanceTime += strtotime($race->getFinishTime());
            }


            $averageMediumDistanceFinishTime = 0;
            if ($mediumDistanceResults) {
                $averageMediumDistanceFinishTime = $totalMediumDistanceTime / count($longDistanceResults);
                $averageMediumDistanceFinishTime = gmdate("H:i:s", $averageMediumDistanceFinishTime);
            }

            $raceData = [
                'title' => $race->getTitle(),
                'Race date' => $race->getDate(), 
                'Distance' => $race->getDistance(), 
                'Average finish time for medium distance' => $averageMediumDistanceFinishTime,
                'Average finish time for long distance' => $averageLongDistanceFinishTime
            ];

            $formattedRaces[] = $raceData;
        }

        return $this->successResponse('Races fetched successfully', $formattedRaces);


    }

}