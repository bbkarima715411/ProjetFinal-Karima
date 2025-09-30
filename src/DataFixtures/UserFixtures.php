<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const REF_PREFIX = 'app_user_';
    public const COUNT = 5;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'admin@enchères.fr', 'password' => 'admin123', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'user1@enchères.fr', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['email' => 'user2@enchères.fr', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['email' => 'user3@enchères.fr', 'password' => 'user123', 'roles' => ['ROLE_USER']],
            ['email' => 'user4@enchères.fr', 'password' => 'user123', 'roles' => ['ROLE_USER']],
        ];

        foreach ($users as $index => $userData) {
            $user = new User();
            $user->setEmail($userData['email'])
                ->setRoles($userData['roles'])
                ->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));

            $manager->persist($user);
            $this->addReference(self::REF_PREFIX.$index, $user);
        }

        $manager->flush();
    }
}
