<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api')]
class ProductController extends AbstractController
{
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

            return new JsonResponse($product, Response::HTTP_CREATED);
        } catch (ORMException $exception) {
            return new JsonResponse(['error' => 'Cannot create product'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/products', name: 'get_all_products', methods: ['GET'])]
    public function getAllProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return new JsonResponse($products, Response::HTTP_OK);
    }

    #[Route('/product/{productId}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($request->get('productId'));

        return new JsonResponse($product, Response::HTTP_OK);
    }
}
