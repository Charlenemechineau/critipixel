<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase; // permet d'utiliser la méthode get() pour faire des requêtes HTTP //
use Symfony\Component\HttpFoundation\Response; // permet de vérifier le code de réponse HTTP//

// Cette classe va me permettre de tester la page de détail d'un jeu vidéo en faisant une requête HTTP et en vérifiant que la réponse est correcte//
final class ShowTest extends FunctionalTestCase
{
    // methode qui permet de tester la page detail d'un jeu et de vérifier que le titre du jeu est bien affiché dans la balise h1//
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }
}
