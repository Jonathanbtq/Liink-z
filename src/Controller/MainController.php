<?php

namespace App\Controller;

use App\Entity\Links;
use App\Form\AddLinkFormType;
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
        $user = $this->getUser();
        $sub = $subRepo->findBy(['subscriber' => $this->getUser()]);
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user,
            'sub' => $sub
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
    #[Route('/{pseudo}', name: 'show')]
    public function showLink($pseudo, LinksRepository $linkRepo, UserRepository $userRepo, SubscriptionRepository $subRepo): Response
    {
        $user = $userRepo->findBy(['pseudo' => $pseudo]);
        $abo = $subRepo->findOneByIdAbo($user, $this->getUser());
        return $this->render('main/show.html.twig', [
            'controller_name' => 'Main page',
            'user' => $linkRepo->findByAdresse($user[0]->getId()),
            'user_main' => $user,
            'abo' => $abo
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
