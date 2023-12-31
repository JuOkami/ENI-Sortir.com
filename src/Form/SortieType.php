<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ avec un label personnalisé
            ->add('nom', TextType::class, ['label' => "Titre de l'evenement : ", 'attr' => ['class' => 'champForm']])
            ->add('dateHeureDebut', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Début de la sortie'])
            ->add('duree', IntegerType::class, ['label' => 'Durée en H : '])
            ->add('dateLimiteInscription', DateTimeType::class, ['widget' => 'single_text', 'label' => "Date limite d'inscription : "])
            ->add('nbInscriptionsMax', IntegerType::class, ['label' => 'Nombre maximum de participants'])
            ->add('infosSortie', TextareaType::class, ['label' => 'Decrivez votre sortie en quelques mots'])
//            ->add('urlPhoto')
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'label' => "Photo de l'evenement"
            ])
            ->add('ville', EntityType::class, ['mapped' => false, 'class' => Ville::class, 'choice_label' => 'nom'])
            ->add('lieu', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'nom'])
            ->add("Valider", SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configure les options par défaut du formulaire
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
