<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LieuController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/enregistrerLieu', name: 'enregistrerLieu', methods: ['POST'])]
    public function enregistrerLieu(
        Request                $request,
        EntityManagerInterface $entityManager,
        VilleRepository        $villeRepository
    )
    {
        // Récupère les données de la requête
        $req = $request->toArray();
        // Crée un nouvel objet Lieu avec les données reçues
        $lieu = (new Lieu())
            ->setNom($req['nom'])
            ->setRue($req['rue'])
            ->setLatitude($req['latitude'])
            ->setLongitude($req['longitude'])
            ->setVille($villeRepository->find($req["ville"]));
        $entityManager->persist($lieu);
        $entityManager->flush();

        // Renvoie une réponse JSON avec la liste mise à jour des lieux
        return $this->json(
            $villeRepository->findBy([], ['nom' => 'ASC']),
            201,
            [],
            ['groups' => 'listeLieux']
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/lieu', name: 'app_lieu')]
    public function creationLieu(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Crée un formulaire de création de lieu en utilisant LieuType
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('app_sorties_creationSortie');

        }

        return $this->render('lieu/creationLieu.html.twig', [
            'lieuForm' => $lieuForm,
            'controller_name' => 'TestController'
        ]);
    }
}
