<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register_user', methods: ['POST'])]
    public function register(
        Request $request,
        JWTTokenManagerInterface $tokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {

        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['password'], $content['email'], $content['firstname'], $content['lastname'])) {
            return new JsonResponse(['error' => 'Missing required fields: login, password, email, firstname, lastname'], Response::HTTP_BAD_REQUEST);
        }

        $emailUser = $em->getRepository(User::class)->findOneBy(['email' => $content['email']]);
        if ($emailUser !== null) {
            return new JsonResponse(['error' => 'Cet email est déjà associé à un compte.'], Response::HTTP_BAD_REQUEST);
        }

        $loginUser = $em->getRepository(User::class)->findOneBy(['login' => $content['login']]);
        if ($loginUser !== null) {
            return new JsonResponse(['error' => 'Ce nom d\'utilisateur est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
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

            return new JsonResponse(['token' => $tokenManager->create($user)], Response::HTTP_CREATED);
        } catch (ORMException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        JWTTokenManagerInterface $tokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {

        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['password'])) {
            return new JsonResponse(['error' => 'Missing required fields: login, password'], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(User::class)->findOneBy(['login' => $content['login']]);
        if ($user !== null && $passwordHasher->isPasswordValid($user, $content['password'])) {
            return new JsonResponse(['token' => $tokenManager->create($user)], Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Mauvais nom d\'utilisateur ou mot de passe.'], Response::HTTP_UNAUTHORIZED);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_get_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        $user = $this->getUser();
        /** @disregard P1013 */
        return $this->json($user->getUserDisplay(), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_update_user', methods: ['PUT'])]
    public function updateCurrentUser(
        Request $request,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $tokenManager,
    ): JsonResponse
    {
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['email'], $content['firstname'], $content['lastname'])) {
            return new JsonResponse(['error' => 'Missing required fields: login, email, firstname, lastname'], Response::HTTP_BAD_REQUEST);
        }

        /** @disregard P1013 */
        if ($user->getEmail() !== $content['email']) {
            $emailUser = $em->getRepository(User::class)->findOneBy(['email' => $content['email']]);
            if ($emailUser !== null) {
                return new JsonResponse(['error' => 'Cet email est déjà associé à un compte.'], Response::HTTP_BAD_REQUEST);
            }
        }

        /** @disregard P1013 */
        if ($user->getLogin() !== $content['login']) {
            $loginUser = $em->getRepository(User::class)->findOneBy(['login' => $content['login']]);
            if ($loginUser !== null) {
                return new JsonResponse(['error' => 'Ce nom d\'utilisateur est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
            }
        }

        $user->setEmail($content['email'])
            ->setLogin($content['login'])
            ->setFirstname($content['firstname'])
            ->setLastname($content['lastname']);

        try {
            $em->persist($user);
            $em->flush();
        } catch (ORMException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        /** @disregard P1013 */
        return $this->json([
            'user' => $user->getUserDisplay(),
            'token' => $tokenManager->create($user)
        ], Response::HTTP_OK);
    }
}
