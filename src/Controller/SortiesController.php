<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_sorties')]
class SortiesController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function listSortie(SortieRepository $sortieRepository): Response
    {

        $sorties = $sortieRepository->findAll();
        return $this->render('sorties/list.html.twig',compact('sorties'));
    }

    #[Route('/detail/{sortie}', name: '_detail')]
    public function detailSortie(Sortie $sortie): Response
    {
        $inscrits = $sortie->getInscriptions();
        return $this->render('sorties/detail.html.twig', compact('sortie', 'inscrits'));
    }

    #[Route('/creationSortie', name: '_creationSortie')]
    public function creationSortie (EntityManagerInterface $entityManager, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted()&&$sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','Sortie ajoutée');
            return $this->redirectToRoute('app_sorties_list');
        }
        return $this->render('sorties/creationSortie.html.twig',
            [
                "sortieForm" => $sortieForm->createView()
            ]
        );
    }
}
