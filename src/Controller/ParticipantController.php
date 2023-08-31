<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModificationProfilType;
use App\Form\ModifierProfilType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/', name: 'app_participant')]
class ParticipantController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/affichageParticipant/{participant}', name: '_affichageParticipant')]
    public function affichageParticipant(Participant $participant): Response
    {
        // Affiche la page de profil d'un participant spécifique
        return $this->render('participant/participant.html.twig', compact('participant'));
    }

    #[IsGranted('ROLE_USER')]
    // Affiche la page de profil de l'utilisateur connecté
    #[Route('/affichageProfil', name: '_affichageProfil')]
    public function affichageUser(): Response
    {
        $participant = $this->getUser(); // Récupère l'utilisateur connecté

        if (!$participant instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté en tant que participant.');
        }
        return $this->render('participant/profilUser.html.twig', compact('participant'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/modifierProfil', name: 'modifier_profil')]
    public function modifierProfil(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $participant = $this->getUser();
        // Crée un formulaire de modification de profil en utilisant ModificationProfilType
        $form = $this->createForm(ModificationProfilType::class, $participant);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_participant_affichageProfil');
        }

        return $this->render('participant/modifierProfil.html.twig', [
            'modifierProfilForm' => $form->createView(),
        ]);
    }
}
