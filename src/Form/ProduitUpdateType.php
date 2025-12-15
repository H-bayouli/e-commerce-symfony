<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\SubCategory;
use App\Entity\Vendeur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('stock')
                        ->add('image', FileType::class,[
                'label'=>'image de produit',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/jpg',
                            'image/png',
                            'image/jpeg'
                        ],
                        'maxSizeMessage'=>'votre image ne doit pas dépasser 1024k',
                        'mimeTypesMessage'=>'Votre image de produit doit être au format jpg ou jpeg ou png'
                    ])
                ]
            ])
            ->add('vendeur', EntityType::class, [
                'class' => Vendeur::class,
                'choice_label' => 'id',
            ])
            ->add('subcategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
