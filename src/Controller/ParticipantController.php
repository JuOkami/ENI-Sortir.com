<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_participant')]
class ParticipantController extends AbstractController
{
    #[Route('/affichageParticipant/{participant}', name: '_affichageParticipant')]
    public function affichageParticipant(Participant $participant): Response
    {
        return $this->render('participant/participant.html.twig', compact('participant'));
    }
    #[Route('/affichageProfil', name: '_affichageProfil')]
    public function affichageUser(): Response
    {
        $participant = $this->getUser(); // Récupère l'utilisateur connecté

        if (!$participant instanceof Participant) {
            throw $this->createAccessDeniedException('Vous devez être connecté en tant que participant.');
        }
        return $this->render('participant/profilUser.html.twig', compact('participant'));
    }
}
