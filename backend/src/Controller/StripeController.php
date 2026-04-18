<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/stripe')]
class StripeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CartRepository $cartRepository
    ) {
    }

    private function getStripeLineItems(array $products): array
    {
        return array_map(
            fn (Product $product) => [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'EUR',
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
            return $this->json(['error' => 'Stripe secret key not configured'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        \Stripe\Stripe::setApiKey($stripeSecretKey);

        $products = $this->cartRepository->getProductsForUser($user);
        if (empty($products)) {
            return $this->json(['error' => 'Cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        $userId = $user->getUserIdentifier();
        /** @disregard P1013 */
        $email = $user->getEmail();
        if (!$email) {
            return $this->json(['error' => 'User email not found'], Response::HTTP_BAD_REQUEST);
        }

        $sessionParams = [
            'mode' => 'payment',
            'client_reference_id' => $userId,
            'customer_email' => $email,
            'shipping_address_collection' => ['allowed_countries' => ['FR']],
            'success_url' => "{$_ENV['APP_URL']}/success",
            'cancel_url' => "{$_ENV['APP_URL']}/",
            'line_items' => $this->getStripeLineItems($products),
        ];

        try {
            $session = \Stripe\Checkout\Session::create($sessionParams);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Stripe checkout session creation failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['url' => $session->url], Response::HTTP_OK);
    }

    #[Route("/hook", name: 'hook', methods: ['POST'])]
    public function stripeHook(Request $request): JsonResponse
    {
        $payload = $request->getContent();

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            return $this->json(['error' => 'Webhook error while parsing request: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
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
                return $this->json(['error' => 'Webhook signature verification failed: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
        }

        if ($event->type === 'checkout.session.completed') {
            $this->handleCheckoutCompleted();
        }

        return $this->json(['status' => 'received'], Response::HTTP_OK);
    }

    private function handleCheckoutCompleted(): void
    {
        $user = $this->getUser();
        if (!$user) {
            return;
        }

        $cartItems = $this->cartRepository->findByUser($user);
        if (empty($cartItems)) {
            return;
        }

        $products = array_map(fn (Cart $item) => $item->getProduct(), $cartItems);
        $order = new Order($user, $products);

        /** @disregard P1013 */
        $user->addOrder($order);

        foreach ($products as $product) {
            $product->setSold(true);
            $this->entityManager->persist($product);
        }

        foreach ($cartItems as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->persist($order);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
