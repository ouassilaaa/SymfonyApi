<?php
namespace App\Service;
use App\Repository\UserRepository;
use App\Service\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    class ApiRegister{

    //fonction pour tester l'authentification
public function authentification(UserPasswordHasherInterface $hash, UserRepository $repo,
string $mail, string $password){
//nettoyer avec la classe Utils et cleanInputStatic
$mail = Utils::cleanInputStatic($mail);
$password = Utils::cleanInputStatic($password);
//récupérer le compte utilisateur
$user = $repo->findOneBy(['email'=>$mail]);
//tester si le compte existe
if($user){
    //tester le password
    if($hash->isPasswordValid($user, $password)){
        return true;
     }
     else{
        return false;
     }
 }
 //test sinon le compte n'existe pas
 else{
    return false;
 }
}
    }
?>