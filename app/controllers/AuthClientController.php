<?php
require_once '../config/database.php';
require_once '../app/models/Client.php';

class AuthClientController {
    private $clientModel;

    public function __construct($pdo){
        $this->clientModel = new Client($pdo);
    }

    public function loginPage($error = ''){
        // Transmission de l'erreur à la vue via la session ou variable
        if(!empty($error)) {
            $_SESSION['error_client'] = $error;
        }
        include '../app/views/client/login.php';
    }

    public function loginSubmit(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CORRECTION : Utilisation de 'password' pour correspondre au HTML
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? ''); 

            if (empty($email) || empty($password)) {
                $this->loginPage("Veuillez remplir tous les champs.");
                exit;
            }

            $clientData = $this->clientModel->getClientByEmail($email);

            // CORRECTION : Comparaison en clair et vérification de l'existence
            if (!$clientData || $password !== $clientData['mot_de_passe']) {
                $this->loginPage("Email ou mot de passe incorrect.");
                exit;
            }

            // CORRECTION : Utilisation de la clé 'client' pour la session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['client'] = $clientData; 

            // Redirection vers le dashboard
            header("Location: index.php?page=client_dashboard");
            exit;
        } else {
            $this->loginPage();
        }
    }

    public function logout(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php?page=choix_role");
        exit;
    }
}