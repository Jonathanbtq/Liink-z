<?php

namespace App\Controller;

use App\Repository\LinksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
