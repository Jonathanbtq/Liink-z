<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\UserModifType;
use App\Repository\LinksRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
            $links->isIsActived(true);
        }else{
            $links->isIsActived(false);
        }
        
        $linksRepo->save($links, true);
        $user = $this->getUser();
        return $this->redirectToRoute('usersettings', ['pseudo' => $user->pseudo]);
    }

    #[Route('/modification/user/{pseudo}', name: 'usermodifaccount')]
    public function userModifAccount(Request $request, $pseudo, UserRepository $userRepo): Response
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

            $userRepo->save($user, true);
            return $this->redirectToRoute('usermodifaccount', ['pseudo' => $user->pseudo]);
        }

        return $this->render('user/accountmodif.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    #[Route('/password/user/{pseudo}', name: 'usermodifpassword')]
    public function userModifPassword($pseudo, Request $request, UserRepository $userRepo, UserPasswordHasherInterface $userPasswordHasher): Response
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
}
