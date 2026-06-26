<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    // Ce test vérifie que la page d'accueil affiche
    // bien les 10 premiers jeux vidéo.
    public function testShouldListTenVideoGames(): void
    {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page est bien accessible (HTTP 200).
        self::assertResponseIsSuccessful();

        // Je vérifie que 10 cartes de jeux vidéo sont affichées.
        self::assertSelectorCount(10, 'article.game-card');
    }

    // Ce test vérifie que la recherche par nom fonctionne correctement.
    public function testShouldFilterVideoGamesBySearch(): void
    {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page est bien chargée.
        self::assertResponseIsSuccessful();

        // Je remplis le champ de recherche puis j'envoie le formulaire.
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

    // Ce test vérifie que si aucun tag n'est sélectionné,
    // tous les jeux vidéo sont affichés.
    public function testShouldListVideoGamesWithoutTagFilter(): void
    {
        // J'ouvre la page d'accueil.
        $this->get('/');

        // Je vérifie que la page est bien accessible.
        self::assertResponseIsSuccessful();

        // J'envoie le formulaire sans sélectionner de tag.
        $this->client->submitForm('Filtrer', [], 'GET');

        // Je vérifie que la page est toujours accessible.
        self::assertResponseIsSuccessful();

        // Je vérifie que les 10 jeux vidéo de la première page sont affichés.
        self::assertSelectorCount(10, 'article.game-card');
    }

    // Ce test vérifie que le filtre par tag fonctionne correctement.
    public function testShouldFilterVideoGamesByExistingTag(): void
    {
        // J'ouvre la page d'accueil et je récupère son contenu.
        $crawler = $this->get('/');

        // Je vérifie que la page est bien chargée.
        self::assertResponseIsSuccessful();

        // Je récupère automatiquement la valeur du premier tag
        // affiché dans le formulaire.
        // Cela évite d'utiliser un identifiant en dur qui peut
        // être différent selon les environnements (local, GitHub...).
        $tagValue = $crawler
            ->filter('input[name="filter[tags][]"]')
            ->first()
            ->attr('value');

        // Je vérifie qu'une valeur de tag a bien été trouvée.
        self::assertNotNull($tagValue);

        // J'envoie le formulaire avec le tag récupéré.
        $this->client->submitForm('Filtrer', [
            'filter[tags]' => [$tagValue],
        ], 'GET');

        // Je vérifie que la page est bien affichée.
        self::assertResponseIsSuccessful();

        // Je vérifie que seuls les jeux vidéo liés à ce tag sont affichés.
        self::assertSelectorCount(2, 'article.game-card');
    }

    // Ce test vérifie le comportement lorsqu'un tag inexistant
    // est envoyé dans l'URL.
    public function testShouldReturnVideoGamesWithUnknownTag(): void
    {
        // J'appelle directement l'URL avec un identifiant de tag
        // qui n'existe pas.
        $this->get('/', [
            'filter' => [
                'tags' => ['9999'],
            ],
        ]);

        // Je vérifie que la page ne renvoie pas d'erreur.
        self::assertResponseIsSuccessful();

        // Je vérifie que l'application affiche la liste complète
        // des jeux vidéo lorsqu'aucun tag valide n'est trouvé.
        self::assertSelectorCount(10, 'article.game-card');
    }
}