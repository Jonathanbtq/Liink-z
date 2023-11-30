<?php

namespace App\Form;

use App\Entity\SocialLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SocialLinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('snapchat', TextType::class, [
                'label' => 'Snapchat',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('instagram', TextType::class, [
                'label' => 'Instagram',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('youtube', TextType::class, [
                'label' => 'Youtube',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('twitch', TextType::class, [
                'label' => 'Twitch',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Facebook',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('twitter', TextType::class, [
                'label' => 'Twitter',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('tiktok', TextType::class, [
                'label' => 'Tiktok',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('whatsapp', TextType::class, [
                'label' => 'WhatsApp',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('amazon', TextType::class, [
                'label' => 'Amazon',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('applemusic', TextType::class, [
                'label' => 'Apple Music',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('discord', TextType::class, [
                'label' => 'Discord',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('github', TextType::class, [
                'label' => 'GitHub',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('kick', TextType::class, [
                'label' => 'Kick',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('etsy', TextType::class, [
                'label' => 'Etsy',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('linkedin', TextType::class, [
                'label' => 'Linkedin',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('patreon', TextType::class, [
                'label' => 'Patreon',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('printerest', TextType::class, [
                'label' => 'Printerest',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('spotify', TextType::class, [
                'label' => 'Spotify',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('telegram', TextType::class, [
                'label' => 'Telegram',
                'attr' => array(
                    'placeholder' => 'https://sociallink.com'
                ),
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Validez'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
