<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Je crée 25 tags de test.
        for ($index = 0; $index < 25; ++$index) {
            $tag = (new Tag())->setName(sprintf('Tag %d', $index));

            // Je prépare le tag pour l'enregistrement en base.
            $manager->persist($tag);
        }

        // Doctrine enregistre réellement les tags en base.
        $manager->flush();
    }
}