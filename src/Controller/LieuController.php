<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu')]
    public function creationLieu(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted()&&$lieuForm->isValid()){
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
