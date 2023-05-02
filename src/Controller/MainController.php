<?php

namespace App\Controller;

use App\Entity\Links;
use App\Form\AddLinkFormType;
use App\Repository\LinksRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(Request $request, LinksRepository $linkRepo): Response
    {
        $link = new Links();
        $form = $this->createForm(AddLinkFormType::class, $link);
        $form->handleRequest($request);

        $user = $this->getUser();
        $pseudo = $user->pseudo;
        if ($form->isSubmitted() && $form->isValid()) {
            $link->setUser($this->getUser());
            $linkRepo->save($link, true);
            return $this->redirectToRoute('main');
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'formLink' => $form->createView(),
            'form' => $linkRepo->findByAdresse($pseudo)
        ]);
    }

    /**
     * Modification des liens utilisateur
     */
    #[Route('/edit{id}', name: 'edit')]
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
    public function showLink($pseudo, LinksRepository $linkRepo, UserRepository $userRepo): Response
    {
        $user = $userRepo->findBy(['pseudo' => $pseudo]);
        return $this->render('main/show.html.twig', [
            'controller_name' => 'Main page',
            'user' => $linkRepo->findByAdresse($user[0]->getId()),
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
