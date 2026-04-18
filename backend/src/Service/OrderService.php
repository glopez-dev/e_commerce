<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
    ) {
    }

    public function createOrderFromCart(User $user): Order
    {
        $cartItems = $this->cartRepository->findByUser($user);

        if (empty($cartItems)) {
            throw new \LogicException('Cart is empty');
        }

        $products = array_map(fn (Cart $item) => $item->getProduct(), $cartItems);
        $order = new Order($user, $products);
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

        return $order;
    }
}
