<?php
require_once '../app/models/Employe.php';

class AuthEmployeController {
    private $employeModel;

    public function __construct($pdo){
        $this->employeModel = new Employe($pdo);
    }

    /**
     * Affiche la page de connexion
     */
    public function loginPage(){
        include '../app/views/employe/login.php';
    }

    /**
     * Traite la tentative de connexion
     */
    public function login() {
        // Pas besoin de session_start complexe ici, il est déjà dans index.php
        // Mais on s'assure qu'elle existe au cas où
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        // Appel au modèle pour vérifier les identifiants
        $employe = $this->employeModel->login($email, $password);

        if ($employe) {
            /**
             * 🔑 CORRECTION MAJEURE ICI :
             * On doit utiliser l'ID de la table UTILISATEUR pour que 
             * les jointures SQL dans le chat fonctionnent.
             * Vérifie que ton modèle Employe retourne bien 'id_utilisateur'.
             */
            $_SESSION['id_utilisateur'] = $employe['id_utilisateur']; 
            $_SESSION['nom'] = $employe['nom'];
            $_SESSION['prenom'] = $employe['prenom'] ?? '';
            $_SESSION['role'] = 'employe';

            header("Location: index.php?page=employe_dashboard");
            exit(); 
        } else {
            $error = "Identifiants invalides ou accès refusé.";
            include '../app/views/employe/login.php';
            exit();
        }
    }

    /**
     * Déconnexion de l'employé
     */
    public function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?page=employe_login");
        exit();
    }
}