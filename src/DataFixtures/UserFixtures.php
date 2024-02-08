<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class UserFixtures extends Fixture
{

    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void 
    {
        $userAdmin = new User();
        $userAdmin->setEmail('admin@symfony-movies.com');
        $userAdmin->setPassword(
            $this->passwordHasher->hashPassword(
                $userAdmin,
                'admin123'
            )
        );
        $manager->persist($userAdmin);

        $userEditor = new User();
        $userEditor->setEmail('editor@symfony-movies.com');
        $userEditor->setPassword(
            $this->passwordHasher->hashPassword(
                $userEditor,
                'editor123'
            )
        );
        $manager->persist($userEditor);

        $user1 = new User();
        $user1->setEmail('user1@symfony-movies.com');
        $user1->setPassword(
            $this->passwordHasher->hashPassword(
                $user1,
                'user123'
            )
        );
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@symfony-movies.com');
        $user2->setPassword(
            $this->passwordHasher->hashPassword(
                $user2,
                'user123'
            )
        );
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('user3@symfony-movies.com');
        $user3->setPassword(
            $this->passwordHasher->hashPassword(
                $user3,
                'user123'
            )
        );
        $manager->persist($user3);

        $manager->flush();
    }
}
