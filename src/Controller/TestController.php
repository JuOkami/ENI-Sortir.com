<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        return $this->render('test/index.html.twig', [
            'lieuForm' => $lieuForm,
            'controller_name' => 'TestController'
        ]);
    }
}
