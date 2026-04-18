<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    #[Route('/register', name: 'app_register_user', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['password'], $content['email'], $content['firstname'], $content['lastname'])) {
            return $this->json(['error' => 'Missing required fields: login, password, email, firstname, lastname'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->userService->isEmailTaken($content['email'])) {
            return $this->json(['error' => 'Cet email est déjà associé à un compte.'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->userService->isLoginTaken($content['login'])) {
            return $this->json(['error' => 'Ce nom d\'utilisateur est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userService->register($content);

        return $this->json(['token' => $this->userService->generateToken($user)], Response::HTTP_CREATED);
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['password'])) {
            return $this->json(['error' => 'Missing required fields: login, password'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userService->authenticate($content['login'], $content['password']);
        if (!$user) {
            return $this->json(['error' => 'Mauvais nom d\'utilisateur ou mot de passe.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(['token' => $this->userService->generateToken($user)], Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_get_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        /** @disregard P1013 */
        return $this->json($this->getUser()->getUserDisplay(), Response::HTTP_OK);
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route(path: '/users', name: 'app_update_user', methods: ['PUT'])]
    public function updateCurrentUser(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $content = json_decode($request->getContent(), true);
        if (!$content || !isset($content['login'], $content['email'], $content['firstname'], $content['lastname'])) {
            return $this->json(['error' => 'Missing required fields: login, email, firstname, lastname'], Response::HTTP_BAD_REQUEST);
        }

        /** @disregard P1013 */
        if ($user->getEmail() !== $content['email'] && $this->userService->isEmailTaken($content['email'], $user)) {
            return $this->json(['error' => 'Cet email est déjà associé à un compte.'], Response::HTTP_BAD_REQUEST);
        }

        /** @disregard P1013 */
        if ($user->getLogin() !== $content['login'] && $this->userService->isLoginTaken($content['login'], $user)) {
            return $this->json(['error' => 'Ce nom d\'utilisateur est déjà utilisé.'], Response::HTTP_BAD_REQUEST);
        }

        $this->userService->updateProfile($user, $content);

        /** @disregard P1013 */
        return $this->json([
            'user' => $user->getUserDisplay(),
            'token' => $this->userService->generateToken($user),
        ], Response::HTTP_OK);
    }
}
