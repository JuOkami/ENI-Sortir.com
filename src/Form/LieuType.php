<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom du lieu : '])
            ->add('rue', TextType::class, ['label'=> 'Nom de la rue : '])
            ->add('latitude', NumberType::class, ['label' => 'Latitude : '])
            ->add('longitude', NumberType::class, ['label'=>'Longitude : '])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'label' => 'Ville : ',
                'choice_label' => function (Ville $ville): string {
                    return ($ville->getCodePostal()).' '.($ville->getNom());
                },
                'query_builder' => function (VilleRepository $er): QueryBuilder {
                return $er->createQueryBuilder('ville')
                    ->orderBy('ville.codePostal', 'ASC');
            },])
            ->add('Valider', SubmitType::class, ['label'=>'Valider le lieu'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
