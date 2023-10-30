<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ResponseTrait
{
    public function successResponse($message, $data = [], $statusCode = 200)
    {
        return new JsonResponse(['message' => $message, 'data' => $data], $statusCode);
    }

    public function errorResponse($message = 'An error occurred', $statusCode = 400)
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }
}