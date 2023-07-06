<?php

namespace App\Controller;

use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/tokenemail', name: 'token_email')]
    public function index(TokenRepository $tokenRepo, UserRepository $userRepo): Response
    {
        $message = '';
        $user = $this->getUser();

        if(isset($_POST['submit_token'])){
            if($token = $tokenRepo->findOneBy(['code' => $_POST['code']])){
                if($token->getPassword() != null){
                    $psw = $token->getPassword();
                    $user = $token->getUser();
    
                    $user->setPassword($psw);
                    $userRepo->save($user, true);
                    $tokenRepo->remove($token, true);
                }else{
                    $user =  $token->getUser();
                    if($token->getEmail() != null){
                        $email = $token->getEmail();
                    }

                    $user->setEmail($email);

                    $userRepo->save($user, true);
                    $tokenRepo->remove($token, true);
                }
                
                return $this->redirectToRoute('usermodifaccount', ['pseudo' => $user->pseudo]);
            }else{
                $message = 'The token is not valid !';
            };
        }
        return $this->render('token/emailtoken.html.twig', [
            'message' => $message
        ]);
    }
}
