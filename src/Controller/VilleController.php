<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville')]
    public function creationVille(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted()&&$villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('app_sorties_creationSortie');

        }

        return $this->render('ville/creationVille.html.twig', [
            'controller_name' => 'VilleController',
            'villeForm'=> $villeForm
        ]);
    }
}
