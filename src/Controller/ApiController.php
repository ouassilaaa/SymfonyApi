<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\ApiRegister;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/verif', name:'app_api_verif')]
    public function verif(ApiRegister $apiRegister, UserPasswordHasherInterface $hash
    , UserRepository $repo, Request $request):Response{

        //récupérer le mail et le password
        $mail = $request->query->get('email');
        $password = $request->query->get('password');

        //tester l'authentification
        if($apiRegister->authentification($hash,$repo, $mail, $password)){
            return $this->json(['connexion'=>'ok'], 200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        else{
            return $this->json(['connexion'=>'invalide'], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
    }


    #[Route('/api/register', name:'app_api_register')]
    public function getToken(Request $request, UserRepository $repo,
        UserPasswordHasherInterface $hash, ApiRegister $apiRegister){
        //récupération du paramètre email
        $mail = $request->query->get('email');
        //récupération du paramètre password
        $password = $request->query->get('password');
        //test si le paramétre mail n'est pas saisi
        if(!$mail OR !$password){
            return $this->json(['Error'=>'informations absentes'], 400,['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*'] );
        }
        //test si le compte est authentifié
        if($apiRegister->authentification($hash,$repo,$mail,$password)){
            //récupération de la clé de chiffrement
            $secretKey = $this->getParameter('token');
            //génération du token
            $token = $apiRegister->genToken($mail, $secretKey, $repo);
            //Retourne le JWT
            return $this->json(['Token_JWT'=>$token], 200, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
        //test si le compte n'est pas authentifié (erreur mail ou password)
        else{
            return $this->json(['Error'=>'Informations de connexion incorrectes'], 400, ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> '*']);
        }
    }
}

?>