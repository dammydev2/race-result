<?php

namespace App\Actions;

use App\Entity\Race;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



class UpdateAction{

    use ResponseTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    function handle(int $raceId, Request $request) : JsonResponse {
        $raceResult = $this->entityManager->getRepository(Race::class)->find($raceId);

        if (!$raceResult) {
            return $this->errorResponse('race not found');
       }

        $data = json_decode($request->getContent(), true);
        
        unset($data['overallPlacement']);
        unset($data['ageCategoryPlacement']);

        foreach ($data as $field => $value) {
            $setter = 'set' . ucfirst($field);
            if (method_exists($raceResult, $setter)) {
                $raceResult->$setter($value);
            }
        }

        $this->entityManager->flush();
        
        return $this->successResponse('Races updated successfully', $raceResult);
    }

}