<?php

namespace Zima\BlogwebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zima\BlogwebBundle\Entity\User;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("fullname", TextType::class)
            ->add("birthday", DateTimeType::class)
            ->add("interests", TextType::class)
            ->add("aboutme", TextareaType::class, array('attr' => array('rows' => '4')))
            ->add("submit", SubmitType::class, array('label' => 'Update'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => User::class]);
    }
}