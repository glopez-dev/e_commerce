<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;

#[Route('/api')]
class ProductController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory,
    ) {
    }

    private function save(Product $product)
    {
        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            $error = ['error' => $exception->getMessage()];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }
    }

    private function validate(Request $request, Product $product = null)
    {
        /* If the request is a POST we need to create a new product. */
        if (!$product) {
            $product = new Product();
        }

        /* Set the authenticated user as the seller */
        $product->setSeller($this->getUser());

        $form = $this->createForm(ProductType::class, $product);

        $content = json_decode($request->getContent(), true);

        $form->submit($content);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @disregard P1013 */
            $this->getUser()->addProduct($product);

            return $product;
        } else {
            $errors = $this->getFormErrors($form);
            $response = ['error' => $errors];
            return $this->json($response, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Retrieves an array of form errors from a given form object.
     *
     * @param FormInterface $form The form object to retrieve errors from.
     * @return array An associative array containing the names of form fields as keys and the corresponding error messages as values.
     */
    private function getFormErrors($form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return $errors;
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/products', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $product = $this->validate($request);
        $this->save($product);

        return $this->json($product, Response::HTTP_CREATED, [], ['groups' => ['api']]);
    }

    #[Route(path: '/products', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $data = $this->productRepository->findBy(['sold' => false]);

        return $this->json($data, Response::HTTP_OK, [], ['groups' => ['api']]);
    }

    #[Route('/products/{id}', methods: ['GET'])]
    public function get(Product $product): JsonResponse
    {
        return $this->json($product->toArray(), Response::HTTP_OK, [], ['groups' => ['api']]);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/{id}', methods: ['PUT'])]
    public function update(Request $request, Product $product): JsonResponse
    {
        $product = $this->validate($request, $product);
        $this->save($product);

        return $this->json($product, Response::HTTP_OK, [], ['groups' => ['api']]);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            $error = ['error' => 'Cannot delete product'];
            return $this->json($error, Response::HTTP_BAD_REQUEST);
        }

        $data = ['message' => 'Product deleted successfully'];
        return $this->json(data: $data, status: Response::HTTP_ACCEPTED);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/products/user/{login}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $request->attributes->get('login')]);
        $products = $user->getProducts();

        return $this->json($products, Response::HTTP_OK, [], ['groups' => ['api']]);
    }
}
