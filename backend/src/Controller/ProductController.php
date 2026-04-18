<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Entity\Product;
use App\Repository\ProductRepository;

#[Route('/api')]
class ProductController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager,
    ) {
    }

    private function updateProductFromRequest(Request $request, Product $product): ?JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (!$content) {
            return $this->json(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'description', 'photo', 'price'];
        foreach ($requiredFields as $field) {
            if (!isset($content[$field])) {
                return $this->json(['error' => "Missing required field: $field"], Response::HTTP_BAD_REQUEST);
            }
        }

        $product->setName($content['name']);
        $product->setDescription($content['description']);
        $product->setPhoto($content['photo']);
        $product->setPrice((float) $content['price']);

        return null;
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/products', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $product = new Product();
        $product->setSeller($this->getUser());

        $error = $this->updateProductFromRequest($request, $product);
        if ($error) {
            return $error;
        }

        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($product, Response::HTTP_CREATED, [], ['groups' => ['api']]);
    }

    #[Route(path: '/products', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        if ($this->getUser() !== null) {
            $data = $this->productRepository->findProductsExceptCurrentUser($this->getUser());
        } else {
            $data = $this->productRepository->findBy(['sold' => false]);
        }

        return $this->json($data, Response::HTTP_OK, [], ['groups' => ['api']]);
    }

    #[Route('/products/{id}', methods: ['GET'])]
    public function get(Product $product): JsonResponse
    {
        return $this->json($product->toArray(), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/{id}', methods: ['PUT'])]
    public function update(Request $request, Product $product): JsonResponse
    {
        if ($product->getSeller() !== $this->getUser()) {
            return $this->json(['error' => 'You are not the owner of this product'], Response::HTTP_FORBIDDEN);
        }

        $error = $this->updateProductFromRequest($request, $product);
        if ($error) {
            return $error;
        }

        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($product, Response::HTTP_OK, [], ['groups' => ['api']]);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        if ($product->getSeller() !== $this->getUser()) {
            return $this->json(['error' => 'You are not the owner of this product'], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            return $this->json(['error' => 'Cannot delete product'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'Product deleted successfully'], Response::HTTP_ACCEPTED);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/user/{login}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $request->attributes->get('login')]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user->getProducts(), Response::HTTP_OK, [], ['groups' => ['api']]);
    }
}
