<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CsvImportType;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Contrôleur pour le tableau de bord EasyAdmin
class adminController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/my-dashboard.html.twig');
    }


    // Méthode pour configurer le tableau de bord EasyAdmin
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ENI Sortir Com'); // Titre du tableau de bord
    }


    // Méthode pour configurer les éléments du menu EasyAdmin
    public function configureMenuItems(): iterable
    {
        // Ajout d'éléments de menu
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-lock');
        yield MenuItem::linkToRoute('Retour du site', 'fa-solid fa-arrow-rotate-left', 'app_sorties_list');
        yield MenuItem::linkToCrud('Participant', 'fa-solid fa-person', Participant::class);
        yield MenuItem::linkToRoute("Importer un fichier", "fa-solid fa-file-import", "app_csv_import");
        yield MenuItem::linkToCrud('Sortie', 'fa-solid fa-calendar-days', Sortie::class);
        yield MenuItem::linkToCrud('Lieu', 'fa-solid fa-shop', Lieu::class);
        yield MenuItem::linkToCrud('Ville', 'fa-solid fa-city', Ville::class);
    }
}
