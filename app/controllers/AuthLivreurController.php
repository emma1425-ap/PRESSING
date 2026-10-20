<?php
require_once '../app/models/Livreur.php';

class AuthLivreurController {
    private $livreurModel;

    public function __construct($pdo){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->livreurModel = new Livreur($pdo);
    }

    public function loginPage(){
        include '../app/views/livreur/login.php';
    }

    public function login(){
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            $error = "Veuillez remplir tous les champs.";
            include '../app/views/livreur/login.php';
            return;
        }

        $user = $this->livreurModel->login($email, $password);

        if ($user) {
            // Variables de session pour le cockpit (Marron/Crème)
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = 'livreur';
            
            header("Location: index.php?page=livreur_dashboard");
            exit;
        } else {
            $error = "Identifiants incorrects ou accès refusé.";
            include '../app/views/livreur/login.php';
        }
    }

    public function logout(){
        session_unset();
        session_destroy();
        header("Location: index.php?page=livreur_login");
        exit;
    }
}