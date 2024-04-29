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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class ProductController extends AbstractController
{

    public function getProductById(int $id, EntityManagerInterface $em): ?Product
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            $error = ['error' => 'Product not found'];
            return $this->json($error, Response::HTTP_NOT_FOUND);
        }

        return $product;
    }

    private function saveProduct(Product $product, EntityManagerInterface $em): ?JsonResponse
    {
        try {
            $em->persist($product);
            $em->flush();
        } catch (ORMException $exception) {
            $error = ['error' => $exception->getMessage()];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = (new Product())
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPhoto($data['photo'])
            ->setPrice($data['price']);

        /** @disregard P1013 */
        $this->getUser()->addProduct($product);

        $this->saveProduct($product, $em);

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/products', name: 'get_all_products', methods: ['GET'])]
    public function getAllProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        $products = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ];
        }, $products);

        return $this->json($products, Response::HTTP_OK);
    }

    #[Route('/product/{productId}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $productId = $request->get('productId');
        $product = $this->getProductById($productId, $entityManager);

        return $this->json($product, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $productId = $request->get('id');
        $product = $this->getProductById($productId, $em);

        $product->setName($data['name'])
            ->setDescription($data['description'])
            ->setPhoto($data['photo'])
            ->setPrice($data['price']);

        $this->saveProduct($product, $em);

        return $this->json($product, Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {

        $productId = $request->get('id');
        $product = $this->getProductById($productId, $em);

        try {
            $em->remove($product);
            $em->flush();
        } catch (ORMException $exception) {
            $error = ['error' => 'Cannot delete product'];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $data = ['message' => 'Product deleted successfully'];
        return $this->json($data, Response::HTTP_ACCEPTED);
    }
}
