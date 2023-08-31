<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CsvImportType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CsvImportController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/csv/import', name: 'app_csv_import')]
    public function importParticipants(
        EntityManagerInterface $entityManager,
        SiteRepository $siteRepository,
        Request $request,
    ): Response
    {

        $form = $this->createForm(CsvImportType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $fichier = $form->get("fichier")->getData();
            $tableur = IOFactory::load($fichier);
            $feuille = $tableur->getActiveSheet();
            //$lignes = $classeur->toArray();

            foreach ($feuille->getRowIterator() as $ligne) {
                $donneesLigne = [];

                $iterateurCellule = $ligne->getCellIterator();
                foreach ($iterateurCellule as $cellule) {
                    $donneesLigne[] = $cellule->getValue();
                }

                // Créez une nouvelle entité Participant et définissez ses propriétés
                $participant = new Participant();
                $participant->setPseudo($donneesLigne[0]);
                $participant->setRoles([$donneesLigne[1]]); // les rôles sont fournis sous forme de tableau []!
                $participant->setPassword('$2y$13$nA382WV0uzwCfYNrrMJ30uN2ZX9LGcJ9uyI3itxqkmKSjaRBDizgK'); //Attention prevoir le hashage du Mdp
                $participant->setNom($donneesLigne[2]);
                $participant->setPrenom($donneesLigne[3]);
                $participant->setTelephone($donneesLigne[4]);
                $participant->setMail($donneesLigne[5]);
                $participant->setActif(true);
                $participant->setSite($siteRepository->find($donneesLigne[6]));

                // Persister et flusher
                $entityManager->persist($participant);
            }
            $entityManager->flush();

            return $this->redirectToRoute("admin");
        }

        // Redirigez vers une page appropriée après l'importation
        return $this->render('csv_import/index.html.twig',compact("form"));

    }
}


