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
        $req = $request->toArray();
        $lieu = (new Lieu())
            ->setNom($req['nom'])
            ->setRue($req['rue'])
            ->setLatitude($req['latitude'])
            ->setLongitude($req['longitude'])
            ->setVille($villeRepository->find($req["ville"]));
        $entityManager->persist($lieu);
        $entityManager->flush();

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
