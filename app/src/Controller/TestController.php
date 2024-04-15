<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'app_test', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'id' => 1,
            'name' => 'Elone le boss',
        ]);
    }
}
