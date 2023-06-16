<?php

namespace App\Controller;

use App\Form\AddLinkFormType;
use App\Repository\LinksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    #[Route('/DelLink/{link}', name: 'deletelink')]
    public function index($link, LinksRepository $linkRepo): Response
    {
        $link = $linkRepo->findOneBy(['id' => $link]);
        $linkRepo->remove($link, true);

        return $this->redirectToRoute('usersettings', ['pseudo' => $this->getUser()->pseudo]);
    }

    #[Route('/Modify/{link}', name: 'modifylink')]
    public function modifyLink($link, LinksRepository $linkRepo, Request $request): Response
    {
        $link = $linkRepo->findOneBy(['id' => $link]);
        $form = $this->createForm(AddLinkFormType::class, $link);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $linkRepo->save($link, true);
            return $this->redirectToRoute('show', ['pseudo' => $this->getUser()->pseudo]);
        }
        return $this->render('link/modifylink.html.twig', [
            'linkForm' => $form->createView(),
        ]);
    }
}
