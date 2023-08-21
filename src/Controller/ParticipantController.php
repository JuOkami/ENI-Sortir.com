<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_participant')]
class ParticipantController extends AbstractController
{
    #[Route('/affichageProfil/{participant}', name: '_affichageProfil')]
    public function affichageProfil(Participant $participant): Response
    {
        return $this->render('participant/profil.html.twig', compact('participant'));
    }
}
