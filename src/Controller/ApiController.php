<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Race;
use App\Validator\Constraints\DateFormat;
use App\Validator\RaceValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class ApiController extends AbstractController
{

    private $raceValidator;

    public function __construct(RaceValidator $raceValidator)
    {
        $this->raceValidator = $raceValidator;
    }
   
    #[Route('/api/imported-races', methods: ['POST', 'HEAD'])]
    public function importResults(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $errors = $this->raceValidator->validate($request->request->all(), $request->files->get('file'));

        if (count($errors) > 0) {
            $jsonResponse = json_encode(['errors' => $errors]);
            return new Response($jsonResponse, Response::HTTP_BAD_REQUEST, ['content-type' => 'application/json']);
        }

        $raceTitle = $request->request->get('title');
        $raceDate = $request->request->get('date');

        $csvFile = $request->files->get('file');

        $csvData = array_map('str_getcsv', file($csvFile->getPathname()));
        $header = array_shift($csvData); // Extract the header row

        $sortedData = [];

        foreach ($csvData as $row) {
            $sortedData[] = array_combine($header, $row);
        }
    

        // Separate long distance results
        $longDistanceResults = array_filter($sortedData, function ($row) {
            return $row['distance'] === 'long';
        });
        
        // Sort long distance results by time
        usort($longDistanceResults, function ($a, $b) {
            return strtotime($a['time']) - strtotime($b['time']);
        });

        $overallPlacement = 1;
        $ageCategoryPlacements = [];

        foreach ($longDistanceResults as $result) {
            
            $raceResult = new Race();
            $raceResult->setFullName($result['fullName']);
            $raceResult->setDistance($result['distance']);
            $raceResult->setFinishTIme($result['time']);
            $raceResult->setAgeCategory($result['ageCategory']);

            // Overall placement
            $raceResult->setOverallPlacement($overallPlacement);
            $overallPlacement++;

            // Age category placements
            $ageCategory = $result['ageCategory'];
            if (!isset($ageCategoryPlacements[$ageCategory])) {
                $ageCategoryPlacements[$ageCategory] = 1;
            }

            $raceResult->setAgeCategoryPlacement($ageCategoryPlacements[$ageCategory]);
            $ageCategoryPlacements[$ageCategory]++;

            $raceResult->setTitle($raceTitle);
            $raceResult->setDate($raceDate);

            $entityManager->persist($raceResult);
        }

        
        // Separate long distance results
        $mediumDistanceResults = array_filter($sortedData, function ($row) {
            return $row['distance'] === 'medium';
        });

        foreach ($mediumDistanceResults as $result) {
            
            $raceResult = new Race();
            $raceResult->setFullName($result['fullName']);
            $raceResult->setDistance($result['distance']);
            $raceResult->setFinishTIme($result['time']);
            $raceResult->setAgeCategory($result['ageCategory']);

            $raceResult->setTitle($raceTitle);
            $raceResult->setDate($raceDate);

            $entityManager->persist($raceResult);
        }
        

        // $entityManager->persist($race);
        $entityManager->flush();

        return $this->json(['message' => 'Race results imported successfully']);

    }

    #[Route('/api/imported-races', methods: ['GET', 'HEAD'])]
    public function getImportedRaces(EntityManagerInterface $entityManager): JsonResponse
    {
        $raceRepository = $entityManager->getRepository(Race::class);
        $races = $raceRepository->findAll();
        
        $formattedRaces = [];
        foreach ($races as $race) {
            $mediumDistanceFinishTime = $race->getMediumDistanceFinishTime();
            $longDistanceFinishTime = $race->getLongDistanceFinishTime();

            $raceData = [
                'Race title' => $race->getTitle(),
                'Race date' => $race->getDate(), 
                'Distance' => $race->getDistance(), 
                'Average finish time for medium distance' => $mediumDistanceFinishTime,
                'Average finish time for long distance' => $longDistanceFinishTime
            ];

            $formattedRaces[] = $raceData;
        }

        return new JsonResponse($formattedRaces);

    }
}