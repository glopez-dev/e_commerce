<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/carts')]
class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CartRepository $cartRepository,
        private OrderService $orderService,
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/{productId}', name: 'add_product', methods: ['POST'])]
    public function addProduct(Request $request): JsonResponse
    {
        $productId = $request->get('productId');
        $product = $this->productRepository->find($productId);

        if (!$product) {
            return $this->json(['error' => "Product #$productId not found"], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        $existing = $this->cartRepository->findOneBy(['_product' => $product, '_user' => $user]);
        if ($existing) {
            return $this->json(['error' => "Product #$productId already in cart"], Response::HTTP_BAD_REQUEST);
        }

        $cart = (new Cart())
            ->setProduct($product)
            ->setUser($user);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $this->json($this->cartRepository->normalizeCartForUser($user), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/', methods: ['GET'])]
    public function getCart(): JsonResponse
    {
        return $this->json($this->cartRepository->normalizeCartForUser($this->getUser()), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/{productId}', name: 'remove_product', methods: ['DELETE'])]
    public function removeProduct(Request $request): JsonResponse
    {
        $productId = $request->get('productId');
        $product = $this->productRepository->find($productId);

        if (!$product) {
            return $this->json(['error' => "Product #$productId not found"], Response::HTTP_NOT_FOUND);
        }

        $cartItems = $this->cartRepository->findBy(['_product' => $product, '_user' => $this->getUser()]);

        if (empty($cartItems)) {
            return $this->json(['error' => "Product #$productId not in cart"], Response::HTTP_BAD_REQUEST);
        }

        foreach ($cartItems as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        return $this->json($this->cartRepository->normalizeCartForUser($this->getUser()), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/validate', name: 'validate', methods: ['PUT'])]
    public function validateCart(): JsonResponse
    {
        try {
            $this->orderService->createOrderFromCart($this->getUser());
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Commande bien validée !'], Response::HTTP_OK);
    }
}
