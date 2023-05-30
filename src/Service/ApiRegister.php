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
    

    public function genToken($mail,$secretKey,$repo){

//autolaod composer
require_once('../vendor/autoload.php');
//Variables pour le token
$issuedAt   = new \DateTimeImmutable();
$expire     = $issuedAt->modify('+60 minutes')->getTimestamp();
$serverName = "your.domain.name";
$username   = $repo->findOneBy(['email'=>$mail])->getNom();
//Contenu du token
$data = [
    'iat'  => $issuedAt->getTimestamp(),         // Timestamp génération du token
    'iss'  => $serverName,                       // Serveur
    'nbf'  => $issuedAt->getTimestamp(),         // Timestamp empécher date antérieure
    'exp'  => $expire,                           // Timestamp expiration du token
    'userName' => $username,                     // Nom utilisateur
];

//implémenter la méthode statique encode de la classe JWT
 $token = JWT :: encode ($data, $secretkey, 'HS512');

//Retourner le token    
 return $token; 
}

public function verifyToken( $token,$secretKey): bool
{
    require_once('../vendor/autoload.php');
    try {
         
        $token = JWT::decode($token, $secretKey, ['HS512']);
  
        return true;
    } catch (\Exception $e) {
       
        return $e->getMessage();
    }
}

}

    
?>