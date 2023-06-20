<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Symfony\Component\Mime\Email;
use App\Repository\ContactRepository;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(MailerInterface $mailer, Request $request, ContactRepository $contactRepo): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if($this->getUser()){
            $contact->setPseudo($this->getUser()->pseudo)
                ->setEmail($this->getUser()->email);
        }

        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();
            $contact->setCreatedAt(new \DateTimeImmutable());
            $contactRepo->save($contact, true);
            
            $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to('contact.pro@jonathanbotquin.com')
                ->subject($contact->getSubject())
                ->text($contact->getMessage())
                ->htmlTemplate('_partials/_contactTemplate.html.twig')

                ->context([
                    'contact' => $contact
                ]);

            $mailer->send($email);

            $this->addFlash(
                'success',
                'Your message as been send with succes !'
            );

            return $this->redirectToRoute('main');
        }
        

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }

    #[Route('/mail', name: 'mail')]
    public function mail(MailerInterface $mailer, Request $request, ContactRepository $contactRepo): Response
    {
        $email = (new TemplatedEmail())
            ->from('jonathan@yahoo.fr')
            ->to('contact.pro@jonathanbotquin.com')
            ->subject('test de sujet')
            ->text('message de qualités++')
            ->htmlTemplate('_partials/contacttemplates/_welcome.html.twig');

        $mailer->send($email);

        return $this->redirectToRoute('main');
    }
}
