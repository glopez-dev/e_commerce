<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $user = $this->getUser();

        /** @disregard P1013 */
        $orders = $user->getOrders()->toArray();

        $normalized_orders = array_map(function ($order) {
            return $order->toArray();
        }, $orders);

        return $this->json($normalized_orders, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/{id}', methods: ['GET'])]
    public function get(Order $order): JsonResponse
    {
        if ($order->getUser() !== $this->getUser()) {
            $error = ['error' => 'You are not allowed to access this order.'];
            return $this->json($error, Response::HTTP_FORBIDDEN);
        }

        return $this->json($order->toArray(), Response::HTTP_OK);
    }
}
