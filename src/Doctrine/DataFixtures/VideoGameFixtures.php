<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();

        /** @var Tag[] $tags */
        $tags = $manager->getRepository(Tag::class)->findAll();

        /** @var VideoGame[] $videoGames */
        $videoGames = [];

        // Je crée automatiquement 50 jeux vidéo.
        for ($index = 0; $index < 50; ++$index) {
            $videoGame = (new VideoGame())
                ->setTitle(sprintf('Jeu vidéo %d', $index))
                ->setDescription($this->faker->paragraphs(10, true))
                ->setReleaseDate(new DateTimeImmutable())
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($index % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $index))
                ->setImageSize(2_098_872);

            // J'associe un tag au jeu vidéo.
            $videoGame->getTags()->add($tags[$index % count($tags)]);

            // Je garde le jeu vidéo dans un tableau pour créer les reviews ensuite.
            $videoGames[] = $videoGame;

            // Je prépare le jeu vidéo pour l'enregistrement.
            $manager->persist($videoGame);
        }

        // J'enregistre les jeux vidéo dans la base.
        $manager->flush();

        // Je crée des reviews pour les 3 premiers utilisateurs de chaque jeu vidéo.
        foreach ($videoGames as $index => $videoGame) {
            foreach (array_slice($users, 0, 3) as $userIndex => $user) {
                $review = (new Review())
                    ->setVideoGame($videoGame)
                    ->setUser($user)
                    ->setRating((($index + $userIndex) % 5) + 1)
                    ->setComment($this->faker->optional()->paragraph());

                $manager->persist($review);
            }

            // Je recalcule les statistiques du jeu après l'ajout des reviews.
            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
        }

        $manager->flush();
    }

    /**
     * @return array<int, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}