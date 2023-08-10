<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Links;
use App\Entity\Contact;
use App\Form\AddLinkFormType;
use App\Form\ImgBackFormType;
use App\Form\ProfilImgFormType;
use App\Form\DetailUserFormType;
use App\Form\IndexContactFormType;
use App\Repository\UserRepository;
use App\Repository\LinksRepository;
use App\Form\ProfilImgProfilFormType;
use App\Repository\ContactRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Request $request, ContactRepository $contactRepo, MailerInterface $mailer, UserRepository $userRepo, SubscriptionRepository $subRepo): Response
    {
        $user = '';
        $errormessage = '';
        if ($this->getUser()) {
            $user = $this->getUser();
            $sub = $subRepo->findSubscriptionsByUser($this->getUser());

            $userAbo = [];
            foreach ($sub as $sub) {
                $userAbo[] = $userRepo->findBy(['id' => $sub->getSubscriptionUser()->getId()], null, 5);
            }
        } else {
            $userAbo = 'Vous n\'etes pas abonnée';
        }

        $contact = new Contact();
        $form = $this->createForm(IndexContactFormType::class, $contact);
        $form->handleRequest($request);
        
        if($this->getUser()){
            $contact->setPseudo($this->getUser()->pseudo)
                ->setEmail($this->getUser()->email)
                ->setSubject('Null');
        }

        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();
            $contact->setCreatedAt(new \DateTimeImmutable());
            $contactRepo->save($contact, true);

            if($contact->getSubject() == null){
                $subject = 'Null';
            }else{
                $subject = $contact->getSubject();
            };
            
            $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->subject($subject)
                ->to('contact.pro@jonathanbotquin.com')
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

        // if(isset($_POST['idx_input_avis'])){
        //     if(!empty($_POST['name']) && !empty($_POST['mail']) && $_POST['text']){
        //         if(is_string(htmlspecialchars($_POST['name'])) && is_string(htmlspecialchars($_POST['mail'])) && is_string(htmlspecialchars($_POST['text']))){
        //             $headers = "From ". $_POST['name'] . " with this email : " . $_POST['mail']. " Le message est :" . $_POST['text'];
        //             mail('botquin.jonathan@yahoo.fr', $_POST['name'], $_POST['text'], $headers);
        //             return $this->redirectToRoute('main');
        //         }else{
        //             $errormessage = 'Please put correct elements';
        //         }
        //     }else{
        //         $errormessage = 'Please indicate all the informations';
        //     }
        // }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user,
            'sub' => $userAbo,
            'errormessage' =>  $errormessage,
            'formcontact' => $form
        ]);
    }

    /**
     * Modification des liens utilisateur
     */
    #[Route('/newlink/{id}', name: 'edit')]
    public function editLink(Request $request, LinksRepository $linkRepo): Response
    {
        $link = new Links();
        $Form = $this->createForm(AddLinkFormType::class, $link);
        $Form->handleRequest($request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            $title = $Form['link']->getdata();
            if(strlen($title) >= 31){
                $title = substr($title, 0, 31);
            }
            if($Form['title']->getdata() == null){
                $link->setTitle($title);
            }
            $link->setIsActived(1);
            $link->setUser($this->getUser());
            $linkRepo->save($link, true);
            return $this->redirectToRoute('show', ['pseudo' => $this->getUser()->pseudo]);
        }

        return $this->render('main/edit.html.twig', [
            'controller_name' => 'Main page',
            'productForm' => $Form->createView()
        ]);
    }

    /***
     * Affichage de la page utilisateur
     */
    #[Route('/{pseudo}', name: 'show')]
    public function showLink($pseudo, LinksRepository $linkRepo, UserRepository $userRepo, SubscriptionRepository $subRepo): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }

        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $abo = $subRepo->findOneByIdAbo($user, $this->getUser());

        return $this->render('main/show.html.twig', [
            'controller_name' => 'Main page',
            'links' => $linkRepo->findByAdresse($user->getId()),
            'user_main' => $userRepo->findBy(['pseudo' => $pseudo]),
            'abo' => $abo
        ]);
    }

    /***
     * Affichage de la page utilisateur
     */
    #[Route('/appearance/{pseudo}', name: 'appearance')]
    public function ChangeUser($pseudo, Request $request, #[Autowire('%photo_dir%')] string $photoDir, #[Autowire('%background_dir%')] string $photoBackDir, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        if(!$this->getUser() === $user){
            return $this->redirectToRoute('login');
        }

        // Formulaire image de profile
        $profilForm = $this->createForm(ProfilImgFormType::class, $user);
        $profilForm->handleRequest($request);

        // Formulaire description
        $form = $this->createForm(DetailUserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $desc = $form['description']->getData();
            $user->setDescription($desc);

            $userRepo->save($user, true);
        }
        $img = $user->getProfileImg();
        if($user->getProfileImg() != null){
            $directory = $photoDir.'/'.$user->id;
        }

        if($profilForm->isSubmitted() && $profilForm->isValid()) {
            $photo = $profilForm['profile_img']->getData();
            $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();

            if(!file_exists($photoDir.'/'.$user->id)){
                $photoDir = $photoDir.'/'.$user->id;
                mkdir($photoDir, 0777);
            }else{
                $objects = scandir($directory);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($directory."/".$object) == "dir"){
                            rmdir($directory."/".$object);
                        }else{
                            unlink($directory."/".$object);
                        }
                    }
                }
                rmdir(strval($directory));
                $photoDir = $photoDir.'/'.$user->id;
                mkdir($photoDir, 0777);
            }
                
                $photo->move($photoDir, $filename);
                $user->setProfileImg($filename);
                
                $userRepo->save($user, true);
                return $this->redirectToRoute('appearance', ['pseudo' => $user->getPseudo()]);
        }

        //Background image
        //*** *//
        $message = '';
        $form_back = $this->createForm(ImgBackFormType::class, $user);
        $form_back->handleRequest($request);
        if($user->getImageBack() != null){
            $directory = $photoBackDir.'/'.$user->id;
        }

        if( $form_back->isSubmitted() && $form_back->isValid()){
            if($img = $form_back['image_back']->getData()){
                $filename = bin2hex(random_bytes(6)) . '.' . $img->guessExtension();
                $ext = ['JPEG', 'jpeg', 'jpg', 'PNG', 'png', 'JPG', 'GIF', 'gif', 'webp'];
                if (in_array(strtolower($img->getClientOriginalExtension()), $ext)) {
                    if(!file_exists($photoBackDir.'/'.$user->id)){
                        $photoBackDir = $photoBackDir.'/'.$user->id;
                        mkdir($photoBackDir, 0777);
                    }else{
                        $objects = scandir($directory);
                        foreach ($objects as $object) {
                            if ($object != "." && $object != "..") {
                                if (filetype($directory."/".$object) == "dir"){
                                    rmdir($directory."/".$object);
                                }else{
                                    unlink($directory."/".$object);
                                }
                            }
                        }
                        rmdir(strval($directory));
                        $photoBackDir = $photoBackDir.'/'.$user->id;
                        mkdir($photoBackDir, 0777);
                    }
                    if($img->move($photoBackDir, $filename)){
                        $message = 'Upload effectué avec succès';
                    }else{
                        $message = 'Erreur lors de l\'upload';
                    }
                    $user->setImageBack($filename);

                    $userRepo->save($user, true);
                    return $this->redirectToRoute('appearance', ['pseudo' => $user->pseudo]);
                }else{
                    $message = 'Extension autorisé (JPEG, JPG, GIF, PNG)';
                }
            }
           
        }

        return $this->render('main/appearance.html.twig', [
            'user_main' => $user,
            'form' => $form->createView(),
            'form_back' => $form_back->createView(),
            'form_img' => $profilForm->createView(),
            'img' => $img,
            'message' => $message
        ]);
    }

    /**
     * Page condition d'utilisation
     */
    #[Route('/legal', name: 'legal')]
    public function showLegal(Request $request, LinksRepository $linkRepo): Response
    {
        return $this->render('conditionlegal/condition.html.twig');
    }
}
