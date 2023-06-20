<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\ImgBackFormType;
use App\Form\UserModifType;
use App\Form\UserPasswordModifType;
use App\Repository\LinksRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;

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
        if($user->isSubscribeAccept() == false){
            $user->setSubscribeAccept(true);
        }else{
            $user->setSubscribeAccept(false);
        }
        
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

    #[Route('/modification/user/{pseudo}', name: 'usermodifaccount')]
    public function userModifAccount(Request $request, $pseudo, MailerInterface $mailer, UserRepository $userRepo): Response
    {
        $message = '';
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);
        if(!$this->getUser()){
            return $this->redirectToRoute('login');
        }
        if(!$this->getUser() === $user){
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(UserModifType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
            $user->setEmail($form->get('email')->getData());

            $email = (new TemplatedEmail())
                ->from('suppchange@linkz.com')
                ->to($form->get('email')->getData())
                ->subject('Password Modification')
                ->htmlTemplate('_partials/contacttemplates/_emailverify.html.twig');

            $mailer->send($email);

            $userRepo->save($user, true);
            return $this->redirectToRoute('usermodifaccount', ['pseudo' => $user->pseudo]);
        }

        return $this->render('user/accountmodif.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    #[Route('/password/user/{pseudo}', name: 'usermodifpassword')]
    public function userModifPassword($pseudo, Request $request, MailerInterface $mailer, UserRepository $userRepo, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $message = '';
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
                    $user->setPassword($hashPassword);

                    $email = (new TemplatedEmail())
                        ->from('suppchange@linkz.com')
                        ->to($this->getUser()->email)
                        ->subject('Password Modification')
                        ->htmlTemplate('_partials/contacttemplates/_passwordmofify.html.twig');

                    $mailer->send($email);

                    return $this->redirectToRoute('main');
                }else{
                    return $message = ['state' => 'errorPassword',
                        'error' => "The password dont be the same"
                    ];
                }
            }else{
                return $message = ['state' => 'errorPasswordRepeat',
                    'error' => "Please repeat you password"
                ];
            }
            $userRepo->save($user, true);
            return $this->redirectToRoute('usermodifaccount', ['pseudo' => $user->pseudo]);
        }

        return $this->render('user/passwordmodif.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

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
                    mkdir($photoDir, 777);
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
                    rmdir($directory);
                    $photoDir = $photoDir.'/'.$user->id;
                    mkdir($photoDir, 777);
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
        return $this->redirectToRoute('usermodifbackground', ['pseudo' => $pseudo]);
    }
}
