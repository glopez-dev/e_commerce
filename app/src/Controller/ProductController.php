<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;


#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'get_all_products', methods: ['GET'], format: 'json')]
    public function getAllProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->json($products);
    }

    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = (new Product())
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPhoto($data['photo'])
            ->setPrice($data['price']);

        try {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->json($product, 201);
        } catch (ORMException $exception) {
            return $this->json(['error' => 'Cannot create product'], 400);
        }
    }
}
