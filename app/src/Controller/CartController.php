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
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Entity\Order;

#[Route('/api')]
class CartController extends AbstractController
{

    private function saveCart(Cart $cart, EntityManagerInterface $em): ?JsonResponse
    {
        try {
            $em->persist($cart);
            $em->flush();
        } catch (\Exception $e) {
            $error = ['error' => $e->getMessage()];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        return null;
    }

    private function isProductInCart(Product $product, EntityManagerInterface $entityManager): bool
    {
        $cart = $entityManager
            ->getRepository(Cart::class)
            ->findOneBy(['_product' => $product, '_user' => $this->getUser()]);

        return $cart != null;
    }

    /**
     * Retrieves the cart items associated with a specific user.
     *
     * @param User $user The user entity for which to retrieve the cart items
     * @param EntityManagerInterface $em The entity manager interface
     * @return array An array of product entities associated with the user's cart
     */
    private function getUserCart(User $user, EntityManagerInterface $em): array
    {
        $cart = $em->getRepository(Cart::class)->findAll(['user' => $user]);

        $products = array_map(fn (Cart $cart) => $cart->getProduct(), $cart);

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
    #[Route('/carts', name: 'get_cart', methods: ['GET'])]
    public function getCart(EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getUserCart($this->getUser(), $em);
        return $this->json($data, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/carts/{productId}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $productId = $request->get('productId');

        $product = (new ProductController())->getProductById($productId, $em);
        $user = $this->getUser();

        if ($this->isProductInCart($product, $em)) {
            $error = ['error' => "Product #$productId already in cart"];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $cart = (new Cart())
            ->setProduct($product)
            ->setUser($user);

        CartController::saveCart($cart, $em);

        /** @disregard P1013 */
        $cart_json = $this->getUserCart($user, $em);

        return $this->json($cart_json, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/carts/{productId}', name: 'remove_from_cart', methods: ['DELETE'])]
    public function removeFromCart(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $productId = $request->get('productId');

        // Get product entity from product id
        $product = (new ProductController())->getProductById($productId, $em);

        // Get cart entity from product and user
        // Note that the relation fields a prefixed with an underscore
        // because entity names are protected to avoid DB conflicts.
        $cart = $em->getRepository(Cart::class)->findBy(['_product' => $product, '_user' => $this->getUser()]);

        // Check if product is in cart
        if ($cart == null) {
            $error = ['error' => "Product #$productId not in cart"];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        try {
            //  Product should be only once in the cart
            //  But the query return an array of entities so we iterate over it just in case.
            foreach ($cart as $item) {
                $em->remove($item);
            }

            // Send all changes made by the ORM to the database
            $em->flush();
        } catch (\Exception $e) {
            $error = ['error' => $e->getMessage()];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        // Then we return the updated cart so the frontend can update it.
        return $this->json($this->getUserCart($this->getUser(), $em), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/carts/validate', name: 'validate_cart', methods: ['PUT'])]
    public function validateCart(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $carts = $em->getRepository(Cart::class)->findBy(['_user' => $user]);
        $products = array_map(fn (Cart $cart) => $cart->getProduct(), $carts);

        $order = new Order($user, $products);

        try {
            $em->persist($order);
            $em->flush();
        } catch (\Exception $e) {
            $error = ['error' => $e->getMessage()];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $json = [
            'id' => $order->getId(),
            'totalPrice' => $order->getTotalPrice(),
            'creationDate' => $order->getCreationDate(),
            'products' => $this->getUserCart($user, $em),
        ];

        return $this->json($json, Response::HTTP_OK);
    }
}
