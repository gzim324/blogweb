<?php

namespace Zima\BlogwebBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zima\BlogwebBundle\Entity\Post;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class)
            ->add("tags", TextType::class)
            ->add("short_description", TextareaType::class, array('attr' => array('rows' => '3')))
//            ->add("contents", CKEditorType::class, array(
//                'config' => array(
//                    'uiColor' => '#ffffff',
//                    'toolbar' => 'full'
//                ),
//            ))
            ->add("contents", CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    'toolbar' => [
                        ["Maximize"],
                        ["Cut", "Copy", "Paste"], ["Undo", "Redo"],
                        ["Bold", "Italic", "Underline", "Strike"], ["NumberedList"], ["BulletedList"], ["Outdent"], ["Indent"], ["Blockquote"],
                        ["Link"], ["Format"], ["JustifyLeft"], ["JustifyCenter"], ["JustifyRight"], ["JustifyBlock"],
                        ["Styles"], ["Scayt"]
                    ],
                ),
            ))
//            ->add("contents", TextareaType::class, array('attr' => array('rows' => '22')))
            ->add("submit", SubmitType::class, array('label' => 'ADD'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Post::class]);
    }
}
