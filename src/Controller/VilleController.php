<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Contrôleur pour les opérations liées aux villes
class VilleController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    // Annotation pour définir la route et le nom, et limiter les méthodes HTTP à POST
    #[Route('/enregistrerVille', name: 'enregistrerVille', methods: ['POST'])]
    public function enregistrerVille(
        Request                $request,
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository
    )
    {
        // Récupération des données de la requête
        $req = $request->toArray();
        // Création d'une nouvelle instance de Ville avec les données reçues
        $ville = (new Ville())
            ->setNom($req['nom'])
            ->setCodePostal($req['codePostal']);

        $entityManager->persist($ville);
        $entityManager->flush();

        // Renvoi des données des villes (triées par nom) sous forme de réponse JSON avec le groupe "listeLieux"
        return $this->json(
            $villeRepository->findBy([], ['nom' => 'ASC']),
            201,
            [],
            ['groups' => 'listeLieux']
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/ville', name: 'app_ville')]
    public function creationVille(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('app_sorties_creationSortie');

        }

        return $this->render('ville/creationVille.html.twig', [
            'controller_name' => 'VilleController',
            'villeForm' => $villeForm
        ]);
    }
}
