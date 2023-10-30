<?php

namespace App\Actions;

use App\Entity\Race;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class CreateAction{

    use ResponseTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Request $request) : Response {
        
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

            $this->entityManager->persist($raceResult);
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

            $this->entityManager->persist($raceResult);
        }
        
        $this->entityManager->flush();

        return $this->successResponse('Result imported successfully');
    }

}
