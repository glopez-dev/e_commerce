<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

#[Route('/api/stripe')]
class StripeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CartRepository $cartRepository
    ) {
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

    private function getStripeLineItems(): array
    {
        $products = $this->getCartedItems();

        if (count($products) == 0) {
            return [];
        }

        return array_map(
            fn (Product $product) => [
                # Create inline Price object
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'EUR',
                    # Create inline Product object
                    'product_data' => [
                        'name' => $product->getName(),
                        'description' => $product->getDescription(),
                        'images' => [$product->getPhoto()],
                    ],
                    'unit_amount_decimal' => $product->getPrice(),
                ],
            ],
            $products
        );
    }


    #[IsGranted('IS_AUTHENTICATED')]
    #[Route("/checkout", name: 'checkout', methods: ['GET'])]
    public function checkout(): JsonResponse
    {
        $user = $this->getUser();
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;
        if ($stripeSecretKey === null) {
            throw new \InvalidArgumentException('Stripe secret key not found');
        }

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $userId = $user->getUserIdentifier();
        /** @disregard P1013 */
        $email = $user->getEmail() ?? throw new \InvalidArgumentException('User email not found');
        $shippingAddressCollection = [
            'allowed_countries' => ['FR']
        ];

        $lineItems = $this->getStripeLineItems();
        if (count($lineItems) == 0) {
            throw new \InvalidArgumentException('Cart is empty');
        }

        $sessionParams = [
            'mode' => 'payment',
            # Client informations
            'client_reference_id' => $userId,
            'customer_email' => $email,
            'shipping_address_collection' => $shippingAddressCollection,
            # Redirection links
            'success_url' => "{$_ENV['APP_URL']}/success",
            'cancel_url' => "{$_ENV['APP_URL']}/",
            # Cart items
            'line_items' => $lineItems,
        ];

        try {
            $session = \Stripe\Checkout\Session::create($sessionParams);
        } catch (\Throwable $e) {
            throw new RuntimeException('Stripe checkout session creation failed: ' . $e->getMessage(), 0, $e);
        }

        return $this->json(['url' => $session->url], Response::HTTP_OK);
    }


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

        $normalized_order = [
            'id' => $order->getId(),
            'totalPrice' => $order->getTotalPrice(),
            'creationDate' => $order->getCreationDate(),
            'products' => $this->normalizeCart(),
        ];

        return $this->json($normalized_order, Response::HTTP_OK);
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




    #[Route("/hook", name: 'hook', methods: ['POST'])]
    public function stripeHook(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $payload = $request->getContent();

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            return $this->json(['error' => 'Webhook error while parsing request: ' . $e->getMessage()], 400);
        }

        $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        if ($endpoint_secret) {
            $signature = $request->headers->get('stripe-signature');

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $signature,
                    $endpoint_secret
                );
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                return $this->json(['error' => 'Webhook signature verification failed: ' . $e->getMessage()], 400);
            }
        }

        if ($event->type === 'checkout.session.completed') {
            $this->createOrder();
        }

        return $this->json(['status' => 'received'], Response::HTTP_OK);
    }
}
