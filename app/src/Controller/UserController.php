<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use \Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;

#[Route('/api')]
class UserController extends AbstractController
{


    public function isLoginOrEmailUsed(array $content, EntityManagerInterface $em): array
    {
        if (null !== $em->getRepository(User::class)->findOneBy(['email' => $content['email']])) {
            return [
                true,
                'email',
                'Cet email est déjà associé à un compte.'
            ];
        }
        if (null !== $em->getRepository(User::class)->findOneBy(['login' => $content['login']])) {
            return [
                true,
                'login',
                'Ce nom d\'utilisateur est déjà utilisé.'
            ];
        }
        return [false, ''];
    }

    public function getContentFromRequest(Request $request, string $route = null): array
    {
        if (empty($request->getContent())) {
            $content['login'] = $request->headers->get('login');
            if ($route === 'app_login' || $route === 'app_register_user') {
                $content['password'] = $request->headers->get('password');
            }
            if ($route === 'app_update_user' || $route === 'app_register_user') {
                $content['firstname'] = $request->headers->get('firstname');
                $content['lastname'] = $request->headers->get('lastname');
                $content['email'] = $request->headers->get('email');
            }
        } else {
            $content = json_decode($request->getContent(), true);
        }
        return $content;
    }

    #[\OpenApi\Attributes\Response(
        response: Response::HTTP_OK,
        description: 'Register a new user and return his JWT Token',
        content: new JsonContent(
            type: 'string',
        )
    )]
    #[Parameter(
        name: 'login',
        description: 'The user login',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'email',
        description: 'The user email',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'password',
        description: 'The user password',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'firstname',
        description: 'The user first name',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'lastname',
        description: 'The user last name',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Tag(name: 'User')]
    #[Route('/register', name: 'app_register_user', methods: ['POST'])]
    public function register(
        Request $request,
        JWTTokenManagerInterface $tokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {

        $content = $this->getContentFromRequest($request, 'app_register_user');

        // Verify if login or email is already use by another user
        $isUsed = $this->isLoginOrEmailUsed($content, $em);
        if ($isUsed[0]) {
            return new JsonResponse([
                'type' =>  $isUsed[1],
                'message' => $isUsed[2]
            ], Response::HTTP_BAD_REQUEST);
        }
        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $content['password']);

        $user->setPassword($hashedPassword)
            ->setEmail($content['email'])
            ->setLogin($content['login'])
            ->setFirstname($content['firstname'])
            ->setLastname($content['lastname']);

        try {
            $em->persist($user);
            $em->flush();

            $data = ['message' => 'Compte créé avec succès.', 'token' => $tokenManager->create($user)];
            return new JsonResponse($data, Response::HTTP_CREATED);
        } catch (ORMException $e) {

            $error = ['message' => $e->getMessage()];
            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }
    }

    #[\OpenApi\Attributes\Response(
        response: Response::HTTP_OK,
        description: 'Log in the user and return his JWT Token',
        content: new JsonContent(
            type: 'string',
        )
    )]
    #[Parameter(
        name: 'login',
        description: 'The user login',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'password',
        description: 'The user password',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Tag(name: 'User')]
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        JWTTokenManagerInterface $tokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {

        $content = $this->getContentFromRequest($request, route: 'app_login');
        $user = $em->getRepository(User::class)->findOneBy(['login' => $content['login']]);

        $isPasswordNotValid = !($passwordHasher->isPasswordValid($user, $content['password']));

        if ($isPasswordNotValid) {
            $error = ['error' => 'Mauvais nom d\'utilisateur ou mot de passe.'];
            return new JsonResponse($error, Response::HTTP_UNAUTHORIZED);
        }

        $data = ['token' => $tokenManager->create($user)];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[\OpenApi\Attributes\Response(
        response: Response::HTTP_OK,
        description: 'Return current user',
        content: new JsonContent(
            type: 'array',
            items: new Items()
        )
    )]
    #[Tag(name: 'User')]
    #[NelmioSecurity(name: 'Bearer')]
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_get_user', methods: ['GET'])]
    public function getCurrentUser(Request $request, Security $security): JsonResponse
    {
        $user = $this->getUser();
        /** @disregard P1013 */
        return $this->json($user->getUserDisplay(), Response::HTTP_OK);
    }

    #[\OpenApi\Attributes\Response(
        response: Response::HTTP_OK,
        description: 'Update user informations and return his new informations',
        content: new JsonContent(
            type: 'string',
        )
    )]
    #[Tag(name: 'User')]
    #[NelmioSecurity(name: 'Bearer')]
    #[Parameter(
        name: 'login',
        description: 'The user login',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'email',
        description: 'The user email',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'firstname',
        description: 'The user first name',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[Parameter(
        name: 'lastname',
        description: 'The user last name',
        in: 'header',
        schema: new Schema(type: 'string')
    )]
    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_update_user', methods: ['PUT'])]
    public function updateCurrentUser(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $content = $this->getContentFromRequest($request, 'app_update_user');

        // Verify if login or email is already use by another user
        $this->isLoginOrEmailUsed($content, $em);

        /** @disregard P1013 */
        $user->setEmail($content['email'])
            ->setLogin($content['login'])
            ->setFirstname($content['firstname'])
            ->setLastname($content['lastname']);

        try {
            $em->persist($user);
            $em->flush();
        } catch (ORMException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        /** @disregard P1013 */
        return $this->json($user->getUserDisplay(), Response::HTTP_OK);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: 'GET')]
    #[Tag(name: 'User')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
