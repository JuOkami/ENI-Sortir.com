<?php

namespace App\Controller;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\SortieFiltre;
use App\Form\SortieFiltreType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use App\Services\InscriptionSortieService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/', name: 'app_sorties')]
class SortiesController extends AbstractController
{

    #[Route('/list', name: '_list')]
    public function listSortie(
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->getUser()){
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        } else {
            $utilisateur = null;
        }

        // Mettre à jour l'état des sorties archivées
        $archivedSorties = $sortieRepository->findArchivableSorties();
        $archivedState = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']);

        foreach ($archivedSorties as $sortie) {
            if ($sortie->getDateHeureDebut() < new DateTime('-1 month')) {
                $sortie->setEtat($archivedState);
                $entityManager->persist($sortie);
            }
        }
        $entityManager->flush();

        $sortieFiltre = new SortieFiltre();
        $sortieFiltreForm = $this->createForm(SortieFiltreType::class, $sortieFiltre);
        $sortieFiltreForm->handleRequest($request);

        if($sortieFiltreForm->isSubmitted()){
            $sorties = $sortieRepository->findByRecherche($sortieFiltre, $utilisateur);
        } else {
            //$sorties = $sortieRepository->findAll();
            $sorties = $sortieRepository->findNonArchivedSorties($archivedState);
        }


        return $this->render('sorties/list.html.twig',[
            "sorties" => $sorties,
            "sortieFiltreForm" => $sortieFiltreForm->createView()
        ]);
    }

    #[Route('/detail/{sortie}', name: '_detail')]
    public function detailSortie(Sortie $sortie, InscriptionSortieService $inscriptionSortieService, ParticipantRepository $participantRepository): Response
    {
        if ($this->getUser()){
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            $isinscrit = $inscriptionSortieService->isInscrit($sortie, $utilisateur);
            $boutonInscription = $inscriptionSortieService->validationInscription($sortie, $utilisateur);
            $boutonDesistement = $inscriptionSortieService->validationDesistement($sortie, $utilisateur);
        } else {
            $isinscrit = false;
            $boutonInscription = ['inscriptionPossible' => false, 'motif' => 'utilisateur non connecté'];
            $boutonDesistement = ['desistementPossible' => false, 'motif' => 'utilisateur non connecté'];
        }

        $inscrits = $sortie->getInscriptions();
        return $this->render('sorties/detail.html.twig', compact('sortie', 'inscrits', 'boutonInscription', 'boutonDesistement', 'isinscrit'));
    }

    #[Route('/inscription/{sortie}', name: '_inscription')]
    public function inscriptionSortie(Sortie $sortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        $sortie->addInscription($utilisateur);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sorties_detail', ["sortie" => $sortie->getId()]);
    }

    #[Route('/desinscription/{sortie}', name: '_desinscription')]
    public function desinscriptionSortie(Sortie $sortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        $sortie->removeInscription($utilisateur);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sorties_detail', ["sortie" => $sortie->getId()]);
    }

    #[Route('/creationSortie', name: '_creationSortie')]
    public function creationSortie (
        EntityManagerInterface $entityManager,
        Request $request,
        VilleRepository $villeRepository,
        LieuRepository $lieuRepository,
        ParticipantRepository $participantRepository,
        EtatRepository $etatRepository,
        SerializerInterface $serializer

    ): Response
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('listeLieux')
            ->toArray();
        $listevilleslieux = $serializer->serialize(
            $villeRepository->findBy([], ["nom" => "ASC"] ),
            'json',
            $context );

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);




        if ($sortieForm->isSubmitted()&&$sortieForm->isValid()){
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            $sortie->setOrganisateur($utilisateur);
            $sortie->setSiteOrganisateur($utilisateur->getSite());
            $sortie->setEtat($etatRepository->find(1));
            $sortie->setLieu($lieuRepository->find($request->query->get("sortie[lieuParVille]")));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie ajoutée');
            return $this->redirectToRoute('app_sorties_list');
        }
        return $this->render('sorties/creationSortie.html.twig',
            [
                "liste" => $listevilleslieux,
                "sortieForm" => $sortieForm->createView()
            ]
        );
    }

    #[Route('/{id}', name: '_supressionSortie', methods: ['POST'])]
    public function supressionSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_list', [], Response::HTTP_SEE_OTHER);
    }

}
