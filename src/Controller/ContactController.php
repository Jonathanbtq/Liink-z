<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

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

            return $this->redirectToRoute('contact');
        }
        

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }
}
