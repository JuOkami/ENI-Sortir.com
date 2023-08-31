<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            yield TextField::new('nom'),
            yield DateField::new('date_heure_debut'),
            yield NumberField::new('duree'),
            yield DateField::new('date_limite_inscription'),
            yield NumberField::new('nb_inscriptions_max'),
            yield TextField::new('infosSortie'),
            yield TextField::new('url_photo'),
            yield AssociationField::new('organisateur'),
            yield AssociationField::new('inscriptions'),
            yield AssociationField::new('siteOrganisateur'),
            yield AssociationField::new('etat'),
            yield AssociationField::new('lieu'),
        ];

    }

}
