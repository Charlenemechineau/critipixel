<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    // Ce test vérifie que la page d'accueil affiche bien les 10 premiers jeux vidéo.
    public function testShouldListTenVideoGames(): void
    {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page s'est chargée correctement (code HTTP 200).
        self::assertResponseIsSuccessful();

        // Je vérifie que 10 cartes de jeux vidéo sont affichées.
        self::assertSelectorCount(10, 'article.game-card');
    }

    // Ce test vérifie que la recherche fonctionne correctement.
    public function testShouldFilterVideoGamesBySearch(): void
    {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page est bien accessible.
        self::assertResponseIsSuccessful();

        // Je remplis automatiquement le champ de recherche
        // avec "Jeu vidéo 49" puis je clique sur le bouton "Filtrer".
        $this->client->submitForm(
            'Filtrer',
            ['filter[search]' => 'Jeu vidéo 49'],
            'GET'
        );

        // Je vérifie que la page s'est bien rechargée.
        self::assertResponseIsSuccessful();

        // Je vérifie qu'un seul jeu vidéo correspond à la recherche.
        self::assertSelectorCount(1, 'article.game-card');
    }

    // Ce DataProvider permet de lancer plusieurs fois le même test
    // avec des données différentes.
    public static function provideTagFilters(): iterable
    {
        // Cas où aucun tag n'est sélectionné.
        yield 'aucun tag' => [
            [],
            10,
        ];

        // Cas où un tag existant est sélectionné.
        yield 'un tag existant' => [
            ['451'],
            2,
        ];
    }

    /**
     * Le DataProvider exécute automatiquement ce test
     * pour chaque jeu de données défini ci-dessus.
     *
     * @dataProvider provideTagFilters
     */
    public function testShouldFilterVideoGamesByTags(
        array $tags,
        int $expectedCount
    ): void {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page est bien chargée.
        self::assertResponseIsSuccessful();

        // Je prépare les données qui seront envoyées dans le formulaire.
        $data = [];

        // Pour chaque tag, je crée le champ attendu par le formulaire.
        foreach ($tags as $index => $tag) {
            $data["filter[tags][$index]"] = $tag;
        }

        // J'envoie le formulaire avec les tags sélectionnés.
        $this->client->submitForm('Filtrer', $data, 'GET');

        // Je vérifie que la page s'affiche correctement.
        self::assertResponseIsSuccessful();

        // Je vérifie que le nombre de jeux vidéo affichés
        // correspond au résultat attendu.
        self::assertSelectorCount($expectedCount, 'article.game-card');
    }

    // Ce test vérifie le comportement lorsqu'un tag qui n'existe pas est envoyé.
    public function testShouldReturnVideoGamesWithUnknownTag(): void
    {
        // J'appelle directement l'URL avec un tag inexistant.
        $this->get('/', [
            'filter' => [
                'tags' => ['9999'],
            ],
        ]);

        // Je vérifie que la page ne génère pas d'erreur.
        self::assertResponseIsSuccessful();

        // Dans cette application, un tag inconnu affiche
        // la liste complète des jeux vidéo.
        self::assertSelectorCount(10, 'article.game-card');
    }
}