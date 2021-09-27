<?php

namespace App\Form;

use App\Entity\ChargeType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => 'charge_type.name',
                'attr'=>['placeholder'=> 'form.placeholder.name']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChargeType::class,
        ]);
    }
}
