<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase; // Me permet de utiliser la méthode get() pour faire des requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // me permet de vérifier le code de réponse HTTP

final class AddReviewTest extends FunctionalTestCase
{
    // methode pour tester l'ajout d'un avis sur un jeu vidéo quand l'utilisateur est connecté
    public function testShouldAddReviewWhenUserIsLoggedIn(): void
    {
        // Arrange : je connecte un utilisateur pour qu'il puisse poster un avis
        $this->login('user+5@email.com');

        // Act : j'ouvre la fiche du jeu vidéo
        $crawler = $this->get('/jeu-video-49');

        // Je vérifie que la page s'affiche correctement (HTTP 200)
        self::assertResponseIsSuccessful();

        // Je récupère le formulaire "Poster" et je le remplis
        $form = $crawler->selectButton('Poster')->form([
            'review[rating]' => 4,
            'review[comment]' => 'Mon commentaire',
        ]);

        // J'envoie le formulaire
        $this->client->submit($form);

        // Assert : je vérifie que Symfony redirige après l'envoi (HTTP 302)
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Je suis automatiquement la redirection
        $this->client->followRedirect();

        // Assert : je vérifie que mon avis apparaît bien dans la page
        self::assertSelectorTextContains('body', 'user+5');
        self::assertSelectorTextContains('body', 'Mon commentaire');
        self::assertSelectorTextContains('body', '4');
    }

    // méthode pour tester que le formulaire d'ajout d'avis
    // n'est pas affiché quand l'utilisateur n'est pas connecté
    public function testShouldNotShowReviewFormWhenUserIsNotLoggedIn(): void
    {
        // Act : j'ouvre la fiche du jeu sans être connectée
        $this->get('/jeu-video-49');

        // Assert : je vérifie que le bouton "Poster" n'est pas affiché
        self::assertSelectorNotExists('button[type="submit"]');
    }
}