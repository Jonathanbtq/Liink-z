<?php

namespace App\Form;

use App\Entity\SocialLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialLinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('snapchat')
            ->add('instagram')
            ->add('youtube')
            ->add('twitch')
            ->add('email')
            ->add('facebook')
            ->add('twitter')
            ->add('tiktok')
            ->add('whatsapp')
            ->add('amazon')
            ->add('applemusic')
            ->add('discord')
            ->add('github')
            ->add('kick')
            ->add('etsy')
            ->add('linkedin')
            ->add('patreon')
            ->add('printerest')
            ->add('spotify')
            ->add('telegram')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
