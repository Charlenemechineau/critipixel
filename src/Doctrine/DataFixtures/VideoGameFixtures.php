<?php

namespace App\Doctrine\DataFixtures;

// Utilisateur//
use App\Model\Entity\User;

// Jeu vidéo//
use App\Model\Entity\VideoGame;

// Service pour calculer la moyenne des notes//
use App\Rating\CalculateAverageRating;

// Service pour compter le nombre de notes//
use App\Rating\CountRatingsPerValue;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

// Tags//
use App\Model\Entity\Tag;
// Reviews (notes + commentaires des utilisateurs)
use App\Model\Entity\Review;

use function array_fill_callback;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        // Faker permet de générer du faux texte automatiquement//
        private readonly Generator $faker,

        // Service qui calcule la moyenne des notes//
        private readonly CalculateAverageRating $calculateAverageRating,

        // Service qui compte le nombre de notes par valeur//
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Je récupère tous les utilisateurs déjà créés//
        $users = $manager->getRepository(User::class)->findAll();

        // Je récupère tous les tags déjà créés//
        $tags = $manager->getRepository(Tag::class)->findAll();

        // Je crée automatiquement 50 jeux vidéo//
        $videoGames = array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame)
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        // J'associe un tag à chaque jeu vidéo//
        foreach ($videoGames as $index => $videoGame) {
            $videoGame->getTags()->add($tags[$index % count($tags)]);
        }

        // Je prépare les jeux vidéo pour l'enregistrement//
        array_walk($videoGames, [$manager, 'persist']);

        // J'enregistre les jeux vidéo dans la base//
        $manager->flush();

        // TODO : ajouter les reviews (commentaires + notes)
        // permet   de créer des reviews pour les 3 premiers utilisateurs pour chaque jeu vidéo, avec des notes allant de 1 à 5 et des commentaires aléatoires//

        // Je parcours tous les jeux vidéo créés
        foreach ($videoGames as $index => $videoGame) {
            foreach (array_slice($users, 0, 3) as $userIndex => $user) { // Je prends les 3 premiers utilisateurs pour créer des reviews//
                $review = (new Review()) // Je crée une nouvelle review pour chaque utilisateur et chaque jeu vidéo//
                    ->setVideoGame($videoGame) // J'associe la review au jeu vidéo actuel//
                    ->setUser($user) // J'associe la review à l'utilisateur actuel//
                    ->setRating((($index + $userIndex) % 5) + 1) //Je génère une note pour la review en fonction de l'index du jeu vidéo et de l'utilisateur pour avoir des notes variées entre 1 et 5//
                    ->setComment($this->faker->optional()->paragraph()); // Je génère un commentaire aléatoire pour la review, avec une probabilité de 50% d'avoir un commentaire (grâce à optional())//

                $manager->persist($review);//Je prépare la review pour l'enregistrement en base//
            }
            $this->calculateAverageRating->calculateAverage($videoGame); // Je calcule la moyenne des notes pour le jeu vidéo actuel en utilisant le service CalculateAverageRating//
            $this->countRatingsPerValue->countRatingsPerValue($videoGame); // Je compte le nombre de notes par valeur pour le jeu vidéo actuel en utilisant le service CountRatingsPerValue//

        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Je demande à Doctrine de charger d'abord
        // les utilisateurs et les tags avant les jeux vidéo.
        // Cela me permet de récupérer les utilisateurs
        // et les tags pour les associer aux jeux vidéo.
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}