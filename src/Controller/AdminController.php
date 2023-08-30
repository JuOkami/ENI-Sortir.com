<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/nouvelUtilisateur', name: 'nouvel_utilisateur')]
public function nouvelUtilisateur(EntityManagerInterface $entityManager,Request $request, UserPasswordHasherInterface $userPasswordHasher,): Response
{
    $user = new Participant();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // encode the plain password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_participant_affichageProfil');
    }
    return $this->render('admin/nouvelUtilisateur.html.twig', [
        'registrationForm' => $form->createView()]);
}
}
