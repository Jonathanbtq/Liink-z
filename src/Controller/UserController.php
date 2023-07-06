<?php

namespace App\Controller;

use Exception;
use App\Entity\Token;
use App\Form\UserModifType;
use App\Entity\Subscription;
use App\Form\ImgBackFormType;
use App\Repository\UserRepository;
use App\Form\UserPasswordModifType;
use App\Repository\LinksRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TokenRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user/subscribe/{showuser}', name: 'subscription')]
    public function subscribe($showuser, SubscriptionRepository $subRepo, UserRepository $userRepo): Response
    {
        $abo = new Subscription();
        $abo->setSubscriber($this->getUser());
        $userfind = $userRepo->findBy(['id' => $showuser]);
        $abo->setSubscriptionUser($userfind[0]);

        $subRepo->save($abo, true);

        $userPseudo = $userfind[0]->pseudo;
        return $this->redirectToRoute('show', ['pseudo' => $userPseudo]);
    }

    #[Route('/user/unsubscribe/{showuser}', name: 'unsubscription')]
    public function unsubscribe($showuser, SubscriptionRepository $subRepo, UserRepository $userRepo): Response
    {
        $desabo = $subRepo->findOneByIdAbo($showuser, $this->getUser());
        $subRepo->remove($desabo, true);

        $userfind = $userRepo->findBy(['id' => $showuser]);
        $userPseudo = $userfind[0]->pseudo;
        return $this->redirectToRoute('show', ['pseudo' => $userPseudo]);
    }

    #[Route('/appearance/{pseudo}/remove', name: 'appearanceDeleteImg')]
    public function imgProfile($pseudo, UserRepository $userRepo): Response
    {
        $user = $userRepo->findBy(['pseudo' => $pseudo]);
        $img = $user[0]->getProfileImg();

        if($img != null){
            unlink("../public/uploads/photos/" . $img);
            $user[0]->setProfileImg(null);
            
            $userRepo->save($user[0], true);
            return $this->redirectToRoute('show', ['pseudo' => $user[0]->pseudo]);
        }else{
            return $this->redirectToRoute('appearanceDeleteImg', ['pseudo' => $user[0]->pseudo]);
        }
    }

    
    #[Route('/settings/{pseudo}', name: 'usersettings')]
    public function userSetting($pseudo, UserRepository $userRepo, LinksRepository $linksRepo): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $links = $linksRepo->findBy(['user' => $this->getUser()]);
        
        return $this->render('user/settings.html.twig', [
            'user' => $user,
            'links' => $links,
        ]);
    }

    #[Route('/settings/{pseudo}', name: 'usersubscribe')]
    public function userSubscribe($pseudo, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $user->setSubscribeAccept(true);
        
        $userRepo->save($user, true);
        return $this->redirectToRoute('usersettings', ['pseudo' => $user->pseudo]);
    }

    #[Route('/settings/{pseudo}', name: 'userunsubscribe')]
    public function userUnSubscribe($pseudo, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $user->setSubscribeAccept(false);
        
        $userRepo->save($user, true);
        return $this->redirectToRoute('usersettings', ['pseudo' => $user->pseudo]);
    }

    #[Route('/settings/links/{link}', name: 'activedlinks')]
    public function userLinksActived($link, LinksRepository $linksRepo): Response
    {
        $links = $linksRepo->findOneBy(['id' => $link]);
        if($links->isIsActived() == false){
            $links->setIsActived(true);
        }else{
            $links->setIsActived(false);
        }
        
        $linksRepo->save($links, true);
        $user = $this->getUser();
        return $this->redirectToRoute('usersettings', ['pseudo' => $user->pseudo]);
    }

    #[Route('/modificationemail', name: 'userpasswordemail')]
    public function userEmailPassword(MailerInterface $mailer, UserRepository $userRepo): Response
    {
        $token = $this->TokenGeneration();

        $tokenValid = new Token();
        
        $tokenValid->setCode($token);
        $tokenValid->setUser($this->getUser());
        $tokenValid->setEmail($this->getUser()->email);

        $email = (new TemplatedEmail())
            ->from('suppchange@linkz.com')
            ->to($this->getUser()->email)
            ->subject('Password Modification')
            ->htmlTemplate('_partials/contacttemplates/_emailtokenverify.html.twig')

            ->context([
                'token' => $token,
                'userpseudo' => $this->getUser()->pseudo
            ]);

        $mailer->send($email);

        return $this->render('user/accountmodif.html.twig', []);
    }

    #[Route('/modification/user/{pseudo}', name: 'usermodifaccount')]
    public function userModifAccount($pseudo, TokenRepository $tokenRepo, MailerInterface $mailer, UserRepository $userRepo): Response
    {
        $message = '';
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $userMail = $user->email;
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        if(!$this->getUser() === $user){
            return $this->redirectToRoute('login');
        }
        // $form = $this->createForm(UserModifType::class, $user);
        // $form->handleRequest($request);

        if(isset($_POST['submit_mail'])){
            if(isset($_POST['email'])){
                // $email = $form->get('email')->getData();
                $email = $_POST['email'];
                $token = $this->TokenGeneration();

                $tokenValid = new Token();
                
                $tokenValid->setCode($token);
                $tokenValid->setUser($this->getUser());
                $tokenValid->setEmail($email);

                // $user->setEmail($form->get('email')->getData());

                $email = (new TemplatedEmail())
                    ->from('suppchange@linkz.com')
                    ->to($email)
                    ->subject('Email Modification')
                    ->htmlTemplate('_partials/contacttemplates/_emailtokenverify.html.twig')

                    ->context([
                        'token' => $token
                    ]);

                $mailer->send($email);

                $tokenRepo->save($tokenValid, true);
                
                return $this->redirectToRoute('token_email');
            }
        }

        return $this->render('user/accountmodif.html.twig', [
            'message' => $message,
            'email' => $userMail
        ]);
    }

    #[Route('/password/user/{pseudo}', name: 'usermodifpassword')]
    public function userModifPassword($pseudo, Request $request, TokenRepository $tokenRepo, MailerInterface $mailer, UserRepository $userRepo, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $message = [];
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        if(!$this->getUser() === $user){
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(UserPasswordModifType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
            $hashPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
                );
            if($form->get('verifypassword')->getData() != null){
                if(password_verify($form->get('verifypassword')->getData(), $hashPassword)){
                    $tokenPass = new Token();

                    $token = $this->TokenGeneration();
                    $tokenPass->setCode($token);

                    $user = $this->getUser();
                    $tokenPass->setEmail($user->email);
                    $tokenPass->setUser($user);
                    $tokenPass->setPassword($hashPassword);

                    $tokenRepo->save($tokenPass, true);

                    $email = (new TemplatedEmail())
                        ->from('suppchange@linkz.com')
                        ->to($this->getUser()->email)
                        ->subject('Password Modification')
                        ->htmlTemplate('_partials/contacttemplates/_passwordmodify.html.twig')

                        ->context([
                            'token' => $token
                        ]);

                    $mailer->send($email);

                    return $this->redirectToRoute('token_email');
                }else{
                    $message = "The password dont be the same";
                }
            }else{
                $message = "Please repeat you password";
            }
        }

        return $this->render('user/passwordmodif.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    // Pas utilisé pour le moment, dans MainController
    // Formulaire d'ajout d'image de BackGround profil
    #[Route('/background/{pseudo}', name: 'usermodifbackground')]
    public function userModifBackGround($pseudo, Request $request, #[Autowire('%background_dir%')] string $photoDir, UserRepository $userRepo, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $message = '';
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        if(!$this->getUser() === $user){
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(ImgBackFormType::class, $user);
        $form->handleRequest($request);
        if($user->getImageBack() != null){
            $directory = $photoDir.'/'.$user->id;
        }

        if( $form->isSubmitted() && $form->isValid()){
            if($img = $form['image_back']->getData()){
                $filename = bin2hex(random_bytes(6)) . '.' . $img->guessExtension();
                // Vérification de l'éxistance d'un fichier nommé à l'id de l'user
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
                if($img->move($photoDir, $filename)){
                    $message = 'Upload effectué avec succès';
                }else{
                    $message = 'Erreur lors de l\'upload';
                }
                $user->setImageBack($filename);
            }
           
            $userRepo->save($user, true);
            return $this->redirectToRoute('appearance', ['pseudo' => $user->pseudo]);
        }

        return $this->render('user/backgroundimgchange.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    #[Route('/deletebackground/{pseudo}', name: 'deletebackground')]
    public function deleteBackground($pseudo, UserRepository $userRepo, #[Autowire('%background_dir%')] string $photoDir): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        $directory = $photoDir.'/'.$user->id;
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
        $user->setImageBack(null);

        $userRepo->save($user, true);
        return $this->redirectToRoute('appearance', ['pseudo' => $pseudo]);
    }

    public function TokenGeneration(){
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < 8; $i++){
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }
}
