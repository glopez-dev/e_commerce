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

class UserController extends AbstractController
{
    #[Route('/api/register', name: 'register_user', methods: ['POST'])]
    public function register(Request $request,
                             JWTTokenManagerInterface $tokenManager,
                             UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface $em)
    : JsonResponse
    {
        $content = json_decode($request->getContent(), true);

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
        } catch (ORMException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'User successfully created',
            'token' => $tokenManager->create($user)
            ], Response::HTTP_CREATED);
    }

    #[Route(path: '/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request,
                             JWTTokenManagerInterface $tokenManager,
                             UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface $em
    ) : JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $user = $em->getRepository(User::class)->findOneBy(['login' => $content['login']]);
        if ($passwordHasher->isPasswordValid($user, $content['password'])) {
            return new JsonResponse(['token' => $tokenManager->create($user)], Response::HTTP_OK);
        }
        return new JsonResponse('Wrong login or password', Response::HTTP_UNAUTHORIZED);
    }

    #[Route(path: '/api/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
