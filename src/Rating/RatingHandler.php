<?php

declare(strict_types=1);

namespace App\Rating;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;

//Permet de calculer la moyenne des notes d'un jeu vidéo et de compter le nombre de notes par valeur (1 à 5) pour un jeu vidéo donné//
final readonly class RatingHandler implements CalculateAverageRating, CountRatingsPerValue
{
    // Permet de calculer la moyenne des notes d'un jeu vidéo donné//
    public function calculateAverage(VideoGame $videoGame): void
    {
        if (count($videoGame->getReviews()) === 0) { // Si le jeu vidéo n'a aucune note, la moyenne est nulle//
            $videoGame->setAverageRating(null); // Je mets la moyenne à null pour indiquer qu'il n'y a pas de notes//
            return;
        }

        // Je calcule la somme des notes en utilisant array_map pour extraire les notes de chaque review et array_sum pour les additionner//
        $ratingsSum = array_sum(
            array_map( //   Je transforme chaque review en sa note correspondante//
                static fn (Review $review): int => $review->getRating(), // Je récupère la note de chaque review//
                $videoGame->getReviews()->toArray() // Je convertis la collection de reviews en tableau pour pouvoir utiliser array_map//
            )
        );
        // Je calcule la moyenne en divisant la somme des notes par le nombre de notes, puis j'arrondis à l'entier supérieur avec ceil pour éviter d'avoir une moyenne avec des décimales//
        $videoGame->setAverageRating((int) ceil($ratingsSum/ count($videoGame->getReviews())));
    }

    //  Permet de compter le nombre de notes par valeur (1 à 5) pour un jeu vidéo donné//
    public function countRatingsPerValue(VideoGame $videoGame): void
    {
        // Je réinitialise le compteur de notes par valeur pour le jeu vidéo avant de le remplir à nouveau, afin d'éviter d'avoir des données obsolètes si les notes ont été modifiées//
        $videoGame->getNumberOfRatingsPerValue()->clear();

        //  Si le jeu vidéo n'a aucune note, il n'y a rien à compter, donc je retourne directement pour éviter de faire des opérations inutiles//
        if (count($videoGame->getReviews()) === 0) {
            return;
        }
        //  Je parcours toutes les reviews du jeu vidéo pour compter le nombre de notes par valeur en utilisant un match pour incrémenter le compteur correspondant à chaque note (1 à 5)//
        foreach ($videoGame->getReviews() as $review) {
            match ($review->getRating()) {
                1 => $videoGame->getNumberOfRatingsPerValue()->increaseOne(),
                2 => $videoGame->getNumberOfRatingsPerValue()->increaseTwo(),
                3 => $videoGame->getNumberOfRatingsPerValue()->increaseThree(),
                4 => $videoGame->getNumberOfRatingsPerValue()->increaseFour(),
                default => $videoGame->getNumberOfRatingsPerValue()->increaseFive(),
            };
        }
    }
}
