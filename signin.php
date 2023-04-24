<?php
include './config/config.php';
// permet d'utiliser des sessions ($_SESSION)
session_start();

//connexion BDD
try {
    $cnx = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=3306', DB_USER, DB_PASSWORD);
    //vérifier s'il existe un utilisateur dans la bdd avec cet email
    $stmt = $cnx->prepare("SELECT * FROM `utilisateurs` WHERE email=:email");
    $email = htmlspecialchars($_POST['email']);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC); // fetch renvoie false si l'email n'est pas dans la bdd

    //vérifier le mot de passe
    if ($utilisateur) {  //équivalent à if($utilisateur != false)
        $mdp = $_POST['password'];
        if (password_verify($mdp, $utilisateur['password'])) {
            $_SESSION['id_utilisateurs'] = $utilisateur['id_utilisateurs'];
            $_SESSION['nom_utilisateurs'] = $utilisateur['nom'];
            $_SESSION['prenom_utilisateurs'] = $utilisateur['prenom'];
            $_SESSION['role_utilisateurs'] = $utilisateur['role'];
            header('location:accueil.php');
        } else {
            header('location:connexion.php');
        }
    } else {
        //mettre l'utilisateur en session
        header('location:connexion.php');
    }
} catch (PDOException $e) {
    // Affichage d'un message d'erreur si la connexion à la base de données a échoué
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}