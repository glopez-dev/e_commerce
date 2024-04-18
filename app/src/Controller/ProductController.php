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
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
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

            return $this->json($product, Response::HTTP_CREATED);
        } catch (ORMException $exception) {
            return $this->json(['error' => 'Cannot create product'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/products', name: 'get_all_products', methods: ['GET'])]
    public function getAllProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->json($products, Response::HTTP_OK);
    }

    #[Route('/product/{productId}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($request->get('productId'));

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = $entityManager->getRepository(Product::class)->find($request->get('id'));
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $product->setName($data['name'])
            ->setDescription($data['description'])
            ->setPhoto($data['photo'])
            ->setPrice($data['price']);

        try {
            $entityManager->persist($product);
            $entityManager->flush();
        } catch (ORMException $exception) {
            return $this->json(['error' => 'Cannot update product'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($product, Response::HTTP_OK);
    }

    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($request->get('id'));
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $entityManager->remove($product);
            $entityManager->flush();
        } catch (ORMException $exception) {
            return $this->json(['error' => 'Cannot delete product'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Product deleted successfully'], Response::HTTP_ACCEPTED);
    }
}
