<?php

namespace App\Form;

use App\Entity\Links;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddLinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('youtube', TextType::class, [
                'label' => 'Ajoute ton lien Youtube',
                'required' => false
            ])
            ->add('twitter', TextType::class, [
                'label' => 'Ajoute ton lien Twitter',
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Ajoute ton lien Facebook',
                'required' => false
            ])
            ->add('instagram', TextType::class, [
                'label' => 'Ajoute ton lien Instagram',
                'required' => false
            ])
            ->add('github', TextType::class, [
                'label' => 'Ajoute ton lien Github',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Links::class,
        ]);
    }
}
