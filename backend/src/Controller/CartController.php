<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Exception;

#[Route('/api/carts')]
class CartController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CartRepository $cartRepository,
    ) {
    }


    private function isProductInCart(Product $product): bool
    {
        $cart = $this->cartRepository->findOneBy(['_product' => $product, '_user' => $this->getUser()]);

        return $cart != null;
    }

    private function normalizeCart(): array
    {
        $user = $this->getUser();
        $carted_items = $this->cartRepository->findAll(['user' => $user]);

        $products = array_map(fn (Cart $carted_item) => $carted_item->getProduct(), $carted_items);

        return array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ];
        }, $products);
    }


    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/{productId}', name: 'add_product', methods: ['POST'])]
    public function addProduct(Request $request): JsonResponse
    {
        $productId = $request->get('productId');

        $product = $this->productRepository->find($productId);
        $user = $this->getUser();

        if ($this->isProductInCart($product)) {
            $error = ['error' => "Product #$productId already in cart"];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $cart = (new Cart())
            ->setProduct($product)
            ->setUser($user);

        $product->addCart($cart);

        $this->entityManager->persist($cart);
        $this->entityManager->persist($product);
        $this->entityManager->flush();


        /** @disregard P1013 */
        $normalized_cart = $this->normalizeCart($cart);

        return $this->json($normalized_cart, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/', methods: ['GET'])]
    public function getCart(): JsonResponse
    {
        $normalized_cart = $this->normalizeCart($this->getUser());
        return $this->json($normalized_cart, Response::HTTP_OK);
    }


    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/{productId}', name: 'remove_product', methods: ['DELETE'])]
    public function removeProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $productId = $request->get('productId');

        $product = $this->productRepository->find($productId);

        $cart = $this->cartRepository->findBy(['_product' => $product, '_user' => $this->getUser()]);

        // Check if product is in cart
        if ($cart == null) {
            $error = ['error' => "Product #$productId not in cart"];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        try {
            // Each product should be only once in the cart
            //  But the query return an array of entities so we iterate over it just in case.
            foreach ($cart as $item) {
                $em->remove($item);
            }

            $em->flush();
        } catch (Exception $error) {
            $data = ['error' => $error->getMessage()];
            return $this->json($data, Response::HTTP_BAD_REQUEST);
        }

        // We return the updated cart so the frontend can update it.
        $normalized_cart = $this->normalizeCart();
        return $this->json($normalized_cart, Response::HTTP_OK);
    }

    private function getCartedItems(): array
    {
        $user = $this->getUser();

        $carted_items = $this->cartRepository->findBy(['_user' => $user]);

        if (count($carted_items) == 0) {
            return [];
        }

        return array_map(fn (Cart $carted_item) => $carted_item->getProduct(), $carted_items);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/validate', name: 'validate', methods: ['PUT'])]
    public function createOrder(): JsonResponse
    {
        $user = $this->getUser();
        $products = $this->getCartedItems();

        if (count($products) == 0) {
            $error = ['error' => "Cart is empty"];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $order = new Order($user, $products);

        /** @disregard P1013 */
        $user->addOrder($order);

        foreach ($carted_items as $item) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }

        try {
            foreach ($products as $product) {
                $product->setSold(true);
                $this->entityManager->persist($product);
            }

            $this->entityManager->persist($order);
            $this->entityManager->persist($user);

            $this->entityManager->flush();
        } catch (Exception $error) {
            $data = ['error' => $error->getMessage()];
            return $this->json($data, Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Commande bien validée !'], Response::HTTP_OK);
    }
}
