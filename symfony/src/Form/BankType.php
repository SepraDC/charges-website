<?php

namespace App\Form;

use App\Entity\Bank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'bank.name',
                'attr'=>['placeholder'=> 'form.placeholder.name']
            ])
            ->add('abbreviation', TextType::class, [
                'label' => 'bank.abbreviation',
                'attr'=>['placeholder'=> 'form.placeholder.abbreviation']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bank::class,
        ]);
    }
}
