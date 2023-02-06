<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                'label' => 'user.username'
            ))
            ->add('roles', ChoiceType::class, array(
                'label' => 'user.roles',
                'mapped'=>false,
                'choices'=> [
                    'user.form.roles.admin'=> 'ROLE_ADMIN',
                    'user.form.roles.user'=> 'ROLE_USER'
                ]
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'=> array('label' => 'user.form.password'),
                'second_options'=>array('label' => 'user.form.repeat_password')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
