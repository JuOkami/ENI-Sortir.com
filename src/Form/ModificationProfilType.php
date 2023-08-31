<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ModificationProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ avec un label personnalisé
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('site', EntityType::class, ['class' => Site::class, 'choice_label' => 'nom'])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'label' => 'Modifiez votre photo de profil',
                'delete_label' => 'Supprimer votre photo de profil actuelle ? ',
                'download_uri' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configuration des options par défaut du formulaire
            'data_class' => Participant::class,
        ]);
    }
}
