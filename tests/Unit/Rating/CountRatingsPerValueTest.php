<?php

declare(strict_types=1);

namespace App\Tests\Unit\Rating;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

final class CountRatingsPerValueTest extends TestCase
{
    public function testShouldCountRatingsPerValue(): void
    {
        // Arrange : je prépare un jeu vidéo avec plusieurs reviews//
        $videoGame = new VideoGame();

        // J'ajoute trois reviews avec des notes différentes//
        $review1 = (new Review())->setRating(2);
        $review2 = (new Review())->setRating(3);
        $review3 = (new Review())->setRating(4);

        //  J'ajoute les reviews au jeu vidéo//
        $videoGame->getReviews()->add($review1);
        $videoGame->getReviews()->add($review2);
        $videoGame->getReviews()->add($review3);

        //  Je crée le service que je veux tester//
        $ratingHandler = new RatingHandler();

        // Act : je compte les notes par valeur
        $ratingHandler->countRatingsPerValue($videoGame);

        // Assert : je vérifie le nombre de notes pour chaque valeur
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        self::assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }

    public function testShouldKeepCountersToZeroWhenNoReviewExists(): void
    {
        // Arrange : je crée un jeu vidéo sans review
        $videoGame = new VideoGame();

        //  Je crée le service que je veux tester//
        $ratingHandler = new RatingHandler();

        // Act : je lance le comptage des notes
        $ratingHandler->countRatingsPerValue($videoGame);

        // Assert : je vérifie que tous les compteurs restent à 0
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        self::assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }
}