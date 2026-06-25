<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Je crée 10 utilisateurs de test.
        for ($index = 0; $index < 10; ++$index) {
            $user = (new User())
                ->setEmail(sprintf('user+%d@email.com', $index))
                ->setPlainPassword('password')
                ->setUsername(sprintf('user+%d', $index));

            $manager->persist($user);
        }

        $manager->flush();
    }
}