<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Commande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName',null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            ->add('lastName',null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            ->add('phone',null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            ->add('email',null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            ->add('adresse',null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            /*
            ->add('createAt', null, [
                'widget' => 'single_text',
            ])
            */
            ->add('city', EntityType::class, [
                'label'=>'ville',
                'class' => City::class,
                'choice_label' => 'name',
            ],null,[
                'attr'=>[
                    'class'=>'form form-control'
                ]
            ])
            ->add('payOnDelivery',null,['label'=>'payer à la livraison'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
