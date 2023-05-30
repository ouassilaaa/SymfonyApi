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

    #[Route('/api/verif-connexion', name: 'verif_connexion', methods: ['GET'])]
    public function verifConnexion(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ApiRegister $apiRegister,
        UserRepository $userRepository
    ): Response{
        // Récupérer le mail et le mot de passe depuis la requête GET
        $email = $request->query->get('email');
        $password = $request->query->get('password');

        // Vérifier l'authentification 
        $isAuthenticated = $apiRegister->authentification($passwordHasher, $userRepository, $email, $password);

        // Retourner la réponse JSON 
        if ($isAuthenticated) {
            return $this->json(['connexion' => 'ok'],200,[]);
        } else {
            return $this->json(['connexion' => 'invalide'],400,[]);
        }
    }
}

?>