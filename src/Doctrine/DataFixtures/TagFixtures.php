<?php

namespace App\Doctrine\DataFixtures;


use App\Model\Entity\Tag;// On importe l'entité Tag pour pouvoir créer des tags//
use Doctrine\Bundle\FixturesBundle\Fixture;// Classe de base des fixtures Symfony//
use Doctrine\Persistence\ObjectManager;// Permet d'enregistrer les objets en base de données//
use function array_fill_callback;// Fonction PHP qui permet de créer un tableau rempli automatiquement//

final class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un tableau contenant 25 objets Tag//
        $tags = array_fill_callback(
            0, // on commence à l'index 0//
            25, // on crée 25 éléments//

            // Pour chaque index on crée un Tag//
            static fn (int $index): Tag =>
            (new Tag())->setName(sprintf('Tag %d', $index))
        );

        // Je prépare tous les tags pour leur futur enregistrement en base.
        // Les données ne sont pas encore enregistrées tant que flush() n'est pas exécuté.
        array_walk($tags, [$manager, 'persist']);

        // Doctrine exécute réellement les requêtes SQL
        // et enregistre tous les tags dans la base de données.
        $manager->flush();
    }
}