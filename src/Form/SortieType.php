<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label'=>"Titre de l'evenement"])
            ->add('dateHeureDebut', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Début de la sortie'])
            ->add('duree', NumberType::class, ['label'=> 'Durée en H : ', 'html5'=>true])
            ->add('dateLimiteInscription', DateTimeType::class, ['widget' => 'single_text', 'label' => "Date limite d'inscription : "])
            ->add('nbInscriptionsMax', NumberType::class, ['label'=>'Nombre maximum de participants'])
            ->add('infosSortie', TextareaType::class, ['label'=>'Decrivez votre sortie en quelques mots'])
            ->add('urlPhoto')
            ->add('choixVille', ChoiceType::class, ['mapped' => false])
            ->add('lieuParVille', ChoiceType::class, ['mapped' => false])
//            ->add('lieu',EntityType::class,['class'=>Lieu::class,'choice_label'=>'nom'])
            ->add("Valider", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
