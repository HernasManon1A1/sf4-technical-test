<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'repositoryName',
                ChoiceType::class,
                [
                    'label' => 'Nom du repository',
                    'required' => true,
                    'expanded' => false,
                    'choices' => $options['repositories'],
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'label' => 'Commentaire',
                    'required' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'repositories' => null,
        ]);
    }
}
