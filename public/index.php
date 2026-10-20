<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // ← NON NÉGOCIABLE

// 2. CONFIGURATION & CONNEXION (Toujours en premier)
require_once '../config/database.php';

try {
    // On crée la connexion PDO d'abord
    $pdo = Database::getConnection(); 
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 3. INCLUSION DES MODÈLES
require_once '../app/models/Client.php';
require_once '../app/models/Employe.php'; 

// 4. INCLUSION DES CONTRÔLEURS
require_once '../app/controllers/AuthClientController.php';
require_once '../app/controllers/AuthLivreurController.php';
require_once '../app/controllers/AuthEmployeController.php';
require_once '../app/controllers/ClientController.php';
require_once '../app/controllers/EmployeController.php'; 
require_once '../app/controllers/LivreurController.php'; 

// ... en haut de index.php
require_once '../app/controllers/AuthLivreurController.php';
$authLivreurCtrl = new AuthLivreurController($pdo);

// 5. INITIALISATION DES CONTRÔLEURS (Maintenant que $pdo existe)
$livreurController = new LivreurController($pdo);
$authCtrl = new AuthEmployeController($pdo);
$empCtrl = new EmployeController($pdo);
$livreurCtrl = new LivreurController($pdo);
$clientCtrl = new ClientController($pdo);
// $clientCtrl = new ClientController($pdo); // Si besoin

// 6. ROUTAGE
$page = $_GET['page'] ?? 'choix_role';

// =============================================================================
// 2. ROUTAGE MULTI-RÔLES
// =============================================================================
switch ($page) {

   // ----------------------- SECTION CLIENT -----------------------
    case 'client_login':
        $auth = new AuthClientController($pdo);
        $auth->loginPage();
        break;

    case 'login_client': 
        $auth = new AuthClientController($pdo);
        $auth->loginSubmit();
        break;

    case 'client_dashboard':
        $controller = new ClientController($pdo);
        $controller->dashboard();
        break;

    case 'client_settings':
        $controller = new ClientController($pdo);
        $controller->settings();
        break;

    case 'traitement_commande':
        // On sort de 'public' avec ../ et on entre dans 'config'
        include '../config/traitement_commande.php'; 
        break;

    case 'traitement_reclamation':
        include '../config/traitement_reclamation.php';
        break;

    case 'client_profil': 
        $controller = new ClientController($pdo);
        $controller->profil();
        break;

    // ACTION : Mise à jour du profil (Nom, Prénom, Email)
    case 'update_settings': 
        $controller = new ClientController($pdo);
        $controller->updateSettings();
        break;

    case 'update_password_action':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_u = $_SESSION['utilisateur']['id_utilisateur'] ?? $_SESSION['client']['id_utilisateur'] ?? null;
            $old_pw = $_POST['old_password'] ?? '';
            $new_pw = $_POST['new_password'] ?? '';

            if ($id_u) {
                $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateur WHERE id_utilisateur = ?");
                $stmt->execute([$id_u]);
                $user = $stmt->fetch();

                if ($user && password_verify($old_pw, $user['mot_de_passe'])) {
                    $new_pw_hashed = password_hash($new_pw, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
                    $update->execute([$new_pw_hashed, $id_u]);
                    
                    // CORRECTION ICI : Rediriger vers 'client_settings' (et non 'setting')
                    header("Location: index.php?page=client_settings&status=success");
                    exit();
                } else {
                    // CORRECTION ICI : Rediriger vers 'client_settings'
                    header("Location: index.php?page=client_settings&status=error_old_pw");
                    exit();
                }
            }
        }
        break;
    case 'nouvelle_commande':
        $controller = new ClientController($pdo);
        $controller->afficherFormulaireCommande();
        break;

    case 'client_submit_order':
        $controller = new ClientController($pdo); // <-- C'ÉTAIT CETTE LIGNE QUI MANQUAIT !
        $controller->submitOrder();
        break;

    case 'creer_reclamation':
        $clientCtrl->creerReclamation();
        break;

     case 'client_commandes': // Historique et Paiement
        $controller = new ClientController($pdo);
        $controller->historique();
        break;

    case 'commande_details':
        $controller = new ClientController($pdo);
        $controller->detailsCommande();
        break;

    case 'reclamations_client':
        $clientCtrl->gererReclamations(); // Maintenant $clientCtrl n'est plus null
        break;

    case 'supprimer_commande':
        include '../config/supprimer_commande.php';
        break;
    // ----------------------- SECTION LIVREUR -----------------------
    case 'livreur_login':
        // Affiche la page de login livreur
        include '../app/views/livreur/login.php';
        break;

    case 'login_process':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // On cherche l'utilisateur par email et mot de passe (en clair)
            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ? AND mot_de_passe = ?");
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch();

            if ($user) {
                // 1. On ouvre la session
                if (session_status() === PHP_SESSION_NONE) session_start();
                
                // 2. On stocke les infos
                $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['role'] = strtolower($user['role']); // On force en minuscule pour éviter les bugs

                // 3. REDIRECTION SELON LE RÔLE
                if ($_SESSION['role'] === 'livreur') {
                    header("Location: index.php?page=livreur_dashboard");
                } elseif ($_SESSION['role'] === 'employe') {
                    header("Location: index.php?page=employe_dashboard");
                } else {
                    header("Location: index.php?page=client_dashboard");
                }
                exit(); // Très important pour arrêter l'exécution ici
            } else {
                // Si erreur, on renvoie vers le login avec un message
                header("Location: index.php?page=livreur_login&error=1");
                exit();
            }
        }
        break;

    case 'livreur_dashboard':
        $livreurCtrl->dashboard(); // Maintenant $livreurCtrl existe et n'est plus null
        break;

    case 'update_livraison':
        $livreurCtrl->updateStatutLivraison();
        break;

    case 'gerer_profil_livreur':
        $livreurCtrl->gererProfil();
        break;

    case 'profil_livreur':
        $livreurCtrl->gererProfil();
        break;

    case 'messages_livreur':
        // CORRIGÉ : On utilise $livreurCtrl au lieu de $livreurController
        $livreurCtrl->afficherMessagesLivreur(); 
        break;

    case 'repondre_reclamation_livreur':
        // CORRIGÉ : On utilise $livreurCtrl
        $livreurCtrl->repondreReclamationLivreur();
        break;

    // ----------------------- SECTION EMPLOYÉ -----------------------
    // 1. Authentification
    case 'employe_login':
        $auth = new AuthEmployeController($pdo);
        $auth->loginPage();
        break;

    case 'login_employe_submit': 
        $auth = new AuthEmployeController($pdo);
        $auth->login(); 
        break;

    // 2. Espace Opérationnel (Sécurisé)
    case 'employe_dashboard':
        $empCtrl = new EmployeController($pdo);
        $empCtrl->dashboard();
        break;

    case 'update_statut_livraison':
        // On appelle la méthode de mise à jour du LivreurController
        $livreurCtrl->updateStatutLivraison(); 
        break;

    // Section Employé dans ton switch
    // ... haut du fichier index.php ...

    case 'nouveau_depot_employe':
        // 1. On doit d'abord créer l'objet sinon il est "null"
        require_once '../app/controllers/EmployeController.php';
        $controller = new EmployeController($pdo); 
        
        // 2. Maintenant on peut appeler la méthode
        $controller->afficherFormulaireDepot();
        break;

    case 'profil_employe':
        $empCtrl->gererProfil();
        break;

    case 'encaisser':
        $empCtrl = new EmployeController($pdo);
        $empCtrl->validerCaisse();
        break;

    case 'assigner_livraison':
        $empCtrl->assignerLivreur();
        break;

    case 'lancer_livraison':
        $empCtrl->lancerLivraison();
        break;

    case 'employe_reclamations':
        $empCtrl->gestionIncidents();
        break;

    case 'update_statut':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->update_statut(); // Utilise bien $empCtrl ici aussi
        break;

    // Dans votre switch($page)
    case 'enregistrer_depot':
        // 1. On inclut le fichier du contrôleur
        require_once '../app/controllers/EmployeController.php';
        
        // 2. On crée l'objet (instanciation)
        // Assurez-vous que $pdo est bien défini au début de votre index.php
        $controller = new EmployeController($pdo);
        
        // 3. On appelle enfin la fonction
        $controller->enregistrer_depot();
        break;

    case 'facturation':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->afficherFacture();
        break;

    case 'valider_paiement':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->encaisserCommande();
        break;

    case 'caisse':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->afficherCaisse(); // On appelle la méthode du contrôleur
        break;

    case 'get_commande_full_details':
        require_once '../app/controllers/EmployeController.php';
        $controller = new EmployeController($pdo);
        $controller->get_commande_full_details();
        break;

    case 'livraison':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        // On déclenche la préparation si l'action est précisée
        if(isset($_GET['action']) && $_GET['action'] == 'preparer'){
            $empCtrl->preparerLivraison();
        }
        break;

    case 'logistique':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->afficherLogistique();
        break;

    case 'valider_livraison':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        $empCtrl->validerLivraison();
        break;

    // ... après tes autres cases (dashboard, livraison, etc.)

case 'reclamations':
    require_once '../app/controllers/EmployeController.php';
    $empCtrl = new EmployeController($pdo);
    
    // Cette route gère l'affichage de la liste ET de la discussion (si un ID est présent)
    $empCtrl->afficherIncidents();
    break;

    case 'repondre_reclamation':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        
        // Cette route reçoit le formulaire POST du chat
        $empCtrl->repondreIncident();
        break;

    case 'cloturer_incident':
        require_once '../app/controllers/EmployeController.php';
        $empCtrl = new EmployeController($pdo);
        
        // Optionnel : Pour le bouton "Marquer résolu"
        $empCtrl->cloturerIncident();
        break;

    // ================= ACTIONS COMMUNES =================
    case 'choix_role':
        include '../app/views/choix_role.php';
        break;

    case 'logout':
        session_start();
        session_destroy();
        header("Location: index.php?page=choix_role");
        exit;
        break;

    // ================= LE DEFAULT (TOUJOURS À LA FIN) =================
   
}