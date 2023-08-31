<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\SortieFiltre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label'=>'Le nom de la sortie contient', "required" => false])
            ->add('dateMin', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Du', "required" => false])
            ->add('dateMax', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Au', "required" => false])
            ->add('isOrganisateur', CheckboxType::class,
                ['label'=>"Evenements dont je suis l'organisateur",
                    "required" => false,
                    "attr"=>["class" => "inputcase"]
                ])
            ->add('isInscrit', ChoiceType::class,
                [
                    'label'=>"Evenements auxquels je suis inscrit",
                    'choices' => [
                        'Inscrit' => true,
                        'Non inscrit' => false,
                        'Les deux' => null,
                    ],
                    'expanded' => true,
                    "attr"=>["class" => "inputcase"]
                ])
            ->add('isPasse', CheckboxType::class, [
                'label'=>"Evenements passés",
                "required" => false,
                "attr"=>["class" => "inputcase"]])
            ->add('site', EntityType::class, ['class'=> Site::class, 'choice_label' => 'nom', "required" => false])
            ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortieFiltre::class,
        ]);
    }
}
