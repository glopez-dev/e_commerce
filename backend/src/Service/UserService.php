<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $tokenManager,
    ) {
    }

    public function register(array $data): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);

        $user->setPassword($hashedPassword)
            ->setEmail($data['email'])
            ->setLogin($data['login'])
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function authenticate(string $login, string $password): ?User
    {
        $user = $this->userRepository->findOneBy(['login' => $login]);

        if ($user !== null && $this->passwordHasher->isPasswordValid($user, $password)) {
            return $user;
        }

        return null;
    }

    public function updateProfile(User $user, array $data): void
    {
        $user->setEmail($data['email'])
            ->setLogin($data['login'])
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function isEmailTaken(string $email, ?User $excludeUser = null): bool
    {
        $existing = $this->userRepository->findOneBy(['email' => $email]);

        return $existing !== null && $existing !== $excludeUser;
    }

    public function isLoginTaken(string $login, ?User $excludeUser = null): bool
    {
        $existing = $this->userRepository->findOneBy(['login' => $login]);

        return $existing !== null && $existing !== $excludeUser;
    }

    public function generateToken(User $user): string
    {
        return $this->tokenManager->create($user);
    }
}
