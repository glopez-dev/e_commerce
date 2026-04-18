<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @return Cart[]
     */
    public function findByUser(User $user): array
    {
        return $this->findBy(['_user' => $user]);
    }

    /**
     * @return Product[]
     */
    public function getProductsForUser(User $user): array
    {
        $cartItems = $this->findByUser($user);

        return array_map(fn (Cart $item) => $item->getProduct(), $cartItems);
    }

    public function normalizeCartForUser(User $user): array
    {
        $products = $this->getProductsForUser($user);

        return array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ];
        }, $products);
    }
}
