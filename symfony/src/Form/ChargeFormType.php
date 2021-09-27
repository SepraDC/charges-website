<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Banque;
use App\Entity\Charge;
use App\Entity\ChargeType;
use App\Entity\Prelevement;

use App\Entity\TypePrelevement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ChargeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bank', EntityType::class, [
                'label' => 'charge.bank',
                'class' => Bank::class,
                'choice_label' => 'name', ])
            ->add('chargeType', EntityType::class, [
                'label' => 'charge.charge_type',
                'class'=> ChargeType::class,
                'choice_label' => 'name'
            ])
            ->add('date', DateType::class, [
                'label' => 'charge.date',
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('name', TextType::class, [
                'label' => 'charge.name',
                'attr'=>['placeholder'=> 'form.placeholder.name']
            ])
            ->add('amount', NumberType::class, [
                'label' => 'charge.amount',
                'attr'=>['placeholder'=> 'form.placeholder.amount']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Charge::class,
        ]);
    }
}
