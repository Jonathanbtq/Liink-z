<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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
}
