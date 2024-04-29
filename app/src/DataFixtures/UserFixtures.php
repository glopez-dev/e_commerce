<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->hasher = $passwordHasher;
    }

    public function getOrder(): int
    {
        return 1;
    }


    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $aldo = new User();
        $aldo->setFirstname($faker->firstName());
        $aldo->setLastname($faker->lastName());
        $aldo->setEmail('aldo@gmail.com');
        $aldo->setPassword($this->hasher->hashPassword($aldo, 'aldo'));
        $aldo->setLogin('aldo');

        $manager->persist($aldo);

        $gabi = new User();
        $gabi->setFirstname($faker->firstName());
        $gabi->setLastname($faker->lastName());
        $gabi->setEmail('gabi@gmail.com');
        $gabi->setLogin('gabi');
        $gabi->setPassword($this->hasher->hashPassword($gabi, 'gabi'));

        $manager->persist($gabi);

        $elone = new User();
        $elone->setFirstname($faker->firstName());
        $elone->setLastname($faker->lastName());
        $elone->setEmail('elon@gmail.com');
        $elone->setLogin('elon');
        $elone->setPassword($this->hasher->hashPassword($elone, 'elon'));

        $manager->persist($elone);
        $manager->flush();
    }
}
