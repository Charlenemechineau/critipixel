<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ReviewType;
use App\List\ListFactory;
use App\List\VideoGameList\Pagination;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/', name: 'video_games_')]
final class VideoGameController extends AbstractController
{

    // Cette méthode permet de lister les jeux vidéo avec pagination et de gérer la requête HTTP pour afficher la liste des jeux vidéo//
    #[Route(name: 'list', methods: [Request::METHOD_GET])]
    public function list(
        #[ValueResolver('pagination')]
        Pagination  $pagination,
        Request     $request,
        ListFactory $listFactory,
    ): Response
    {
        $videoGamesList = $listFactory->createVideoGamesList($pagination)->handleRequest($request);

        return $this->render('views/video_games/list.html.twig', ['list' => $videoGamesList]);
    }

    // Cette méthode permet d'afficher la fiche d'un jeu vidéo
   // et de gérer l'ajout d'un avis (review) par un utilisateur.
    #[Route('{slug}', name: 'show', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(VideoGame $videoGame, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Création d'une nouvelle review vide qui sera remplie par le formulaire
        $review = new Review();

        // Création du formulaire d'ajout d'avis et récupération des données envoyées
        $form = $this->createForm(ReviewType::class, $review)->handleRequest($request);

        // Vérifie que le formulaire a été envoyé et que les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifie que l'utilisateur a le droit de laisser un avis sur ce jeu vidéo
            $this->denyAccessUnlessGranted('review', $videoGame);

            // Associe l'avis au jeu vidéo concerné
            $review->setVideoGame($videoGame);

            // Associe l'avis à l'utilisateur connecté
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $review->setUser($user);

            // Prépare l'enregistrement en base de données
            $entityManager->persist($review);

            // Exécute réellement l'insertion en base de données
            $entityManager->flush();

            // Redirige l'utilisateur vers la fiche du jeu vidéo après l'ajout de l'avis
            return $this->redirectToRoute('video_games_show', ['slug' => $videoGame->getSlug()]);
        }

        // Affiche la page du jeu vidéo ainsi que le formulaire d'ajout d'avis
        return $this->render('views/video_games/show.html.twig', [
            'video_game' => $videoGame,
            'form' => $form
        ]);
    }
}
