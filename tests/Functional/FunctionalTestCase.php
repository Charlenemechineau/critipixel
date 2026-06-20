<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser; // Me permet de faire des requêtes HTTP dans mes tests//
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase; // Me permet de  faire des tests fonctionnels dans Symfony//
use Symfony\Component\DomCrawler\Crawler; // Me permet de parcourir le contenu HTML de la réponse HTTP pour vérifier que les éléments sont présents//

//Cette classe va permettre de  faire des tests fonctionnels dans Symfony en utilisant le client HTTP
// pour faire des requêtes et vérifier les réponses.
// Elle fournit également des méthodes utilitaires pour interagir avec la base de données et se connecter en tant qu'utilisateur.
abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->service(EntityManagerInterface::class);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    protected function login(string $email = 'user+0@email.com'): void
    {
        $user = $this->service(EntityManagerInterface::class)->getRepository(User::class)->findOneByEmail($email);

        $this->client->loginUser($user);
    }
}
