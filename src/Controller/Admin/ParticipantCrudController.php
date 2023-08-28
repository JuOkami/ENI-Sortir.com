<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('pseudo');
        yield TextField::new('nom');
        yield TextField::new('prenom');
        yield TextField::new('telephone');
        yield TextField::new('mail');
        yield ChoiceField::new('actif', 'État')
            ->setChoices([
                'Actif' => 1,
                'Inactif' => 0,
            ])
            ->allowMultipleChoices(false) // Ne permettre qu'un seul choix
            ->setRequired(true);

        // Ajoutez le champ 'plainPassword'
        yield TextField::new('plainPassword', 'Mot de Passe')
            ->setFormType(PasswordType::class)
            ->onlyOnForms() // Ne l'affichez que dans les formulaires
            ->setFormTypeOptions([
                'hash_property_path' => 'password',
                'required' => true,
                'mapped' => false, // Ne pas mapper à l'entité
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre mot de passe devrait contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);

        // Ajoutez le champ 'site'
        yield AssociationField::new('site');

        // Ajoutez d'autres champs si nécessaire
    }
}