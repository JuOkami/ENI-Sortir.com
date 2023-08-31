<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\SortieFiltre;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieFiltreType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use App\Services\InscriptionSortieService;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\Clock\now;


#[Route('/', name: 'app_sorties')]
class SortiesController extends AbstractController
{

    #[Route('/', name: '_list')]
    public function listSortie(
        SortieRepository       $sortieRepository,
        ParticipantRepository  $participantRepository,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->getUser()) {
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            if (!$utilisateur->isActif()) {
                return $this->redirectToRoute('app_logout');
            }
        } else {
            $utilisateur = new Participant();
        }

        $sortieFiltre = new SortieFiltre();
        $sortieFiltreForm = $this->createForm(SortieFiltreType::class, $sortieFiltre);
        $sortieFiltreForm->handleRequest($request);

        if ($sortieFiltreForm->isSubmitted()) {
            $sorties = $sortieRepository->findByRecherche($sortieFiltre, $utilisateur);
        } else {
            // TODO Trie par date
            $sorties = $sortieRepository->findAll();
        }


        return $this->render('sorties/list.html.twig', [
            "sorties" => $sorties,
            "sortieFiltreForm" => $sortieFiltreForm->createView()
        ]);
    }

    #[Route('/aboutus', name: '_aboutus')]
    public function aboutus(): Response
    {
        return $this->render('sorties/aboutus.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/detail/{sortie}', name: '_detail')]
    public function detailSortie(Sortie $sortie, InscriptionSortieService $inscriptionSortieService, ParticipantRepository $participantRepository): Response
    {
        if ($this->getUser()) {
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
        $endDateTime = clone $sortie->getDateHeureDebut();
        $endDateTime->add(new DateInterval('PT' . $sortie->getDuree() . 'H'));

        return $this->render('sorties/detail.html.twig', compact('sortie', 'inscrits', 'endDateTime', 'boutonInscription', 'boutonDesistement', 'isinscrit'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/inscription/{sortie}', name: '_inscription')]
    public function inscriptionSortie(Sortie $sortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez si la sortie est en état "Annulée"
        if ($sortie->getEtat()->getId() === 6) {
            $this->addFlash('error', "L'inscription à cette sortie n'est pas possible car elle est annulée.");
            return $this->redirectToRoute('app_sorties_detail', ["sortie" => $sortie->getId()]);
        }

        $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        $sortie->addInscription($utilisateur);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sorties_detail', ["sortie" => $sortie->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/desinscription/{sortie}', name: '_desinscription')]
    public function desinscriptionSortie(Sortie $sortie, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        $sortie->removeInscription($utilisateur);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('app_sorties_detail', ["sortie" => $sortie->getId()]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/creationSortie', name: '_creationSortie')]
    public function creationSortie(
        EntityManagerInterface $entityManager,
        Request                $request,
        VilleRepository        $villeRepository,
        ParticipantRepository  $participantRepository,
        EtatRepository         $etatRepository,
        SerializerInterface    $serializer
    ): Response
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('listeLieux')
            ->toArray();
        $listevilleslieux = $serializer->serialize(
            $villeRepository->findBy([], ["nom" => "ASC"]),
            'json',
            $context);

        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
//        $villeForm->handleRequest($request);

        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
//        $lieuForm->handleRequest($request);

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            $sortie->setOrganisateur($utilisateur);
            $sortie->setSiteOrganisateur($utilisateur->getSite());
            $sortie->setEtat($etatRepository->find(1));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie ajoutée');
            return $this->redirectToRoute('app_sorties_list');
        }
        return $this->render('sorties/creationSortie.html.twig',
            [
                "liste" => $listevilleslieux,
                "sortieForm" => $sortieForm->createView(),
                "lieuForm" => $lieuForm->createView(),
                "villeForm" => $villeForm->createView()
            ]
        );
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/annulation/{id}', name: '_annuleeSortie', methods: ['POST'])]
    public function annuleeSortie(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response

    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $etatAnnulee = $entityManager->getRepository(Etat::class)->find(6);
            $sortie->setEtat($etatAnnulee);

            // Détacher tous les participants de la sortie
            foreach ($sortie->getInscriptions() as $participant) {
                $sortie->removeInscription($participant);
                $participant->removeInscription($sortie);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sorties_list', [], Response::HTTP_SEE_OTHER);
    }

}
