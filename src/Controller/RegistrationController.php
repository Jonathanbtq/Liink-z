<?php

namespace App\Controller;

use App\Entity\Links;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\LinksRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Part\DataPart;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, LinksRepository $linkRepo, ContactRepository $contactRepo, MailerInterface $mailer, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $message = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCertified(0);
            $user->setSubscribeAccept(1);
            if ($userRepo->findOneByPseudo($form['pseudo']->getData()) != null) {
                $message = True;
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'message' => $message
                ]);
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);

            $contact = $form->getData();
            $email = (new TemplatedEmail())
                ->from('contact@Linkz.com')
                ->subject('Welcome In Link\'z')
                ->to($contact->getEmail())
                ->htmlTemplate('_partials/contacttemplates/_welcome.html.twig')

                ->context([
                    'contact' => $contact
                ]);

            $mailer->send($email);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'message' => $message
        ]);
    }
}
