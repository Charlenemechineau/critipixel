<?php
// fichier qui va me permettre de tester la classe qui calcule la moyenne des notes d'un jeu vidéo//
declare(strict_types=1);

namespace App\Tests\Unit\Rating;

use PHPUnit\Framework\TestCase; // permet de faire des assertions pour vérifier que les résultats obtenus sont corrects//
use App\Model\Entity\Review; // permet de créer des reviews pour les jeux vidéo//
use App\Model\Entity\VideoGame; // permet de créer des jeux vidéo pour les tests//
use App\Rating\RatingHandler; // permet de calculer la moyenne des notes d'un jeu vidéo pour les tests//

final class AverageRatingCalculatorTest extends TestCase
{
    //me permet detester la méthode qui calcule la moyenne des notes d'un jeu vidéo//
    public function testShouldCalculateAverageRating(): void
    {
        // Arrange : je prépare les données du test
        // Je crée un jeu vidéo
        $videoGame = new VideoGame();

        // Je crée trois reviews avec des notes différentes
        $review1 = (new Review())->setRating(2);
        $review2 = (new Review())->setRating(3);
        $review3 = (new Review())->setRating(4);

        // J'ajoute les reviews au jeu vidéo
        $videoGame->getReviews()->add($review1);
        $videoGame->getReviews()->add($review2);
        $videoGame->getReviews()->add($review3);

        // Je crée le service que je veux tester
        $ratingHandler = new RatingHandler();

        // Act : j'appelle la méthode à tester
        $ratingHandler->calculateAverage($videoGame);

        // Assert : je vérifie le résultat
        self::assertEquals(3, $videoGame->getAverageRating());
    }

    // me permet de tester la méthode qui calcule la moyenne des notes d'un jeu vidéo quand il n'y a pas de review//
    public function testShouldReturnNullWhenNoReviewExists(): void
    {
        // Arrange : je crée un jeu vidéo sans review
        $videoGame = new VideoGame();

        $ratingHandler = new RatingHandler();

        // Act : je calcule la moyenne
        $ratingHandler->calculateAverage($videoGame);

        // Assert : je vérifie que la moyenne est null
        self::assertNull($videoGame->getAverageRating());
    }
}