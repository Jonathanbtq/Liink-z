<?php

namespace App\Controller;

use App\Entity\Links;
use App\Entity\User;
use App\Form\AddLinkFormType;
use App\Form\DetailUserFormType;
use App\Repository\LinksRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Request $request, LinksRepository $linkRepo, UserRepository $userRepo, SubscriptionRepository $subRepo): Response
    {
        if($this->getUser()){
            $user = $this->getUser();
            $sub = $subRepo->findSubscriptionsByUser($this->getUser());

            $userAbo = [];
            foreach($sub as $sub){
                $userAbo[] = $userRepo->findBy(['id' => $sub->getSubscriptionUser()->getId()]);
            }
        }else{
            $userAbo = 'Vous n\'etes pas abonnée';
        }
        
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user,
            'sub' => $userAbo
        ]);
    }

    /**
     * Modification des liens utilisateur
     */
    #[Route('/edit/{id}', name: 'edit')]
    public function editLink(Request $request, links $link, LinksRepository $linkRepo): Response
    {
        $Form = $this->createForm(AddLinkFormType::class, $link);
        $Form->handleRequest($request);

        if ($Form->isSubmitted() && $Form->isValid()) {
            $linkRepo->save($link, true);
            return $this->redirectToRoute('main');
        }

        return $this->render('main/edit.html.twig', [
            'controller_name' => 'Main page',
            'productForm' => $Form->createView()
        ]);
    }

    /***
     * Affichage de la page utilisateur
     */
    #[Route('/show/{pseudo}', name: 'show')]
    public function showLink($pseudo, Request $request, LinksRepository $linkRepo, UserRepository $userRepo, SubscriptionRepository $subRepo, User $userc): Response
    {
        $user = $userRepo->findBy(['pseudo' => $pseudo]);
        $abo = $subRepo->findOneByIdAbo($user, $this->getUser());

        if(isset($_POST['addDescShow'])){
            if (isset($_POST['description']) && !empty($_POST['description'])){
                $monInputValue = $request->request->get('description');
                $desc = $user[0];
                $desc->setDescription($monInputValue);

                $userRepo->save($desc, true);
                return $this->redirectToRoute('show', ['pseudo' => $user[0]->getPseudo()]);
            }
        }
       

        return $this->render('main/show.html.twig', [
            'controller_name' => 'Main page',
            'user' => $linkRepo->findByAdresse($user[0]->getId()),
            'user_main' => $userRepo->findBy(['pseudo' => $pseudo]),
            'abo' => $abo
        ]);
    }

    /***
     * Affichage de la page utilisateur
     */
    #[Route('/appearance/{pseudo} ', name: 'appearance')]
    public function ChangeUser($pseudo, Request $request, LinksRepository $linkRepo, UserRepository $userRepo, SubscriptionRepository $subRepo, User $userc): Response
    {
        $user = $userRepo->findBy(['pseudo' => $pseudo]);
        $form = $this->createForm(DetailUserFormType::class, $user[0]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $userRepo->save($user[0], true);
            return $this->redirectToRoute('show', ['pseudo' => $user[0]->getPseudo()]);
        }

        return $this->render('main/appearance.html.twig', [
            'user_main' => $user,
            'form' => $form->createView(),
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
