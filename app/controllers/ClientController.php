<?php
require_once '../app/models/Client.php';

class ClientController {
    private $clientModel;
    private $pdo; // Stockage de la connexion pour les requêtes directes

    public function __construct($pdo){
        $this->pdo = $pdo;
        $this->clientModel = new Client($pdo);
    }

    /**
     * Utilitaire : Vérifie si le client est connecté
     */
    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['client'])) {
            header("Location: index.php?page=client_login");
            exit;
        }
        return $_SESSION['client'];
    }

    // --- AFFICHAGE ---

    public function dashboard(){
        $client = $this->checkSession();
        $id_client = $client['id_client'];

        $commandes = $this->clientModel->getCommandes($id_client) ?: [];
        $factures = $this->clientModel->getFactures($id_client) ?: [];
        
        include '../app/views/client/dashboard.php';
    }
    

    public function settings() {
        $client = $this->checkSession();
        $status = $_GET['status'] ?? null; // Pour afficher les alertes de succès/erreur
        include '../app/views/client/settings.php';
    }

    public function profil() {
        $client = $this->checkSession();
        include '../app/views/client/profil.php';
    }

    // --- GESTION DES COMMANDES ---

    public function commandes(){
        $client = $this->checkSession();
        $commandes = $this->clientModel->getCommandes($client['id_client']) ?: [];
        include '../app/views/client/commandes.php';
    }

    public function afficherFormulaireCommande() {
        $client = $this->checkSession();
        include '../app/views/client/nouvelle_commande.php';
    }

    public function submitOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = $this->checkSession();
            $id_client = $client['id_client']; 
            $vêtement = $_POST['vêtement_type'] ?? 'Non spécifié';
            $service = $_POST['service'] ?? 'Standard';
            $notes = $_POST['notes'] ?? '';
            $date_retrait = $_POST['date_retrait'] ?? null;

            $res = $this->clientModel->createCommande($id_client, $vêtement, $service, $notes, $date_retrait);

            if ($res) {
                header("Location: index.php?page=client_dashboard&success=order_ok");
            } else {
                echo "Erreur lors de l'enregistrement.";
            }
            exit;
        }
    }

    // --- HISTORIQUE & DÉTAILS ---

    public function historique() {
        $client = $this->checkSession();
        $historique = $this->clientModel->getFullHistory($client['id_client']);
        include '../app/views/client/historique.php';
    }

    public function detailsCommande() {
        $client = $this->checkSession();
        $id_commande = $_GET['id'] ?? 0;
        $details = $this->clientModel->getOrderDetails($id_commande);
        include '../app/views/client/details.php';
    }

    public function factures(){
        $client = $this->checkSession();
        $factures = $this->clientModel->getFactures($client['id_client']) ?: [];
        include '../app/views/client/factures.php';
    }

    // --- ACTIONS DE MISE À JOUR ---

    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = $this->checkSession();
            $id_user = $client['id_utilisateur'];

            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];

            if ($this->clientModel->updateProfile($id_user, $nom, $prenom, $email)) {
                $_SESSION['client']['nom'] = $nom;
                $_SESSION['client']['prenom'] = $prenom;
                $_SESSION['client']['email'] = $email;
                header("Location: index.php?page=client_profil&success=1");
                exit;
            }
        }
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = $this->checkSession();
            $id_u = $client['id_utilisateur']; 
            
            $old_pw = $_POST['old_password'] ?? '';
            $new_pw = $_POST['new_password'] ?? '';

            // Requête préparée sur la table utilisateur
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM utilisateur WHERE id_utilisateur = ?");
            $stmt->execute([$id_u]);
            $user = $stmt->fetch();

            if ($user && password_verify($old_pw, $user['mot_de_passe'])) {
                $new_pw_hashed = password_hash($new_pw, PASSWORD_DEFAULT);
                $update = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
                $update->execute([$new_pw_hashed, $id_u]);

                header("Location: index.php?page=client_settings&status=success");
                exit();
            } else {
                header("Location: index.php?page=client_settings&status=error_old_pw");
                exit();
            }
        }
    }

    public function gererReclamations() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Vérification de la session
        $id_u = $_SESSION['client']['id_utilisateur'] ?? $_SESSION['id_utilisateur'] ?? null;
        if (!$id_u) {
            header("Location: index.php?page=choix_role");
            exit();
        }

        // 1. Récupérer l'ID client
        $stmt = $this->pdo->prepare("SELECT id_client FROM client WHERE id_utilisateur = ?");
        $stmt->execute([$id_u]);
        $id_client = $stmt->fetchColumn();

        // 2. Récupérer les COMMANDES (pour le menu déroulant)
        $stmtCmd = $this->pdo->prepare("SELECT id_commande, date_commande FROM commande WHERE id_client = ? ORDER BY date_commande DESC");
        $stmtCmd->execute([$id_client]);
        $mes_commandes = $stmtCmd->fetchAll();

        // 3. Récupérer les RÉCLAMATIONS
        $stmtRec = $this->pdo->prepare("SELECT r.* FROM reclamation r 
                                        JOIN commande c ON r.id_commande = c.id_commande 
                                        WHERE c.id_client = ? ORDER BY r.id_reclamation DESC");
        $stmtRec->execute([$id_client]);
        $reclamations = $stmtRec->fetchAll();

        // 4. Récupérer TOUS LES MESSAGES de discussion pour ces réclamations
        $stmtMsg = $this->pdo->prepare("SELECT * FROM reclamation_messages 
                                        WHERE id_reclamation IN (
                                            SELECT id_reclamation FROM reclamation r 
                                            JOIN commande c ON r.id_commande = c.id_commande 
                                            WHERE c.id_client = ?
                                        ) ORDER BY date_envoi ASC");
        $stmtMsg->execute([$id_client]);
        $all_messages = $stmtMsg->fetchAll();

        // On passe les variables à la vue
        include '../app/views/client/reclamation_form.php';
    }

    public function supprimerMaCommande() {
        $id_commande = $_GET['id'] ?? null;
        $id_client = $_SESSION['id_utilisateur'] ?? null; // Sécurité pour vérifier que c'est bien sa commande

        if ($id_commande && $id_client) {
            try {
                // On commence une "Transaction" pour être sûr que tout se supprime ou rien du tout
                $this->pdo->beginTransaction();

                // 1. SUPPRIMER LE PAIEMENT (L'enfant)
                // On supprime d'abord dans la table paiement car elle dépend de commande
                $stmt1 = $this->pdo->prepare("DELETE FROM paiement WHERE id_commande = ?");
                $stmt1->execute([$id_commande]);

                // 2. SUPPRIMER LES ÉVENTUELS MESSAGES OU RÉCLAMATIONS (Si besoin)
                $stmt2 = $this->pdo->prepare("DELETE FROM reclamation WHERE id_commande = ?");
                $stmt2->execute([$id_commande]);

                // 3. ENFIN, SUPPRIMER LA COMMANDE (Le parent)
                $stmt3 = $this->pdo->prepare("DELETE FROM commande WHERE id_commande = ? AND id_client = ?");
                $stmt3->execute([$id_commande, $id_client]);

                $this->pdo->commit();
                
                header("Location: index.php?page=mes_commandes&success=supprime");
                exit();

            } catch (Exception $e) {
                // En cas d'erreur, on annule tout pour ne pas corrompre la base
                $this->pdo->rollBack();
                die("Erreur lors de la suppression : " . $e->getMessage());
            }
        }
    }

    public function repondreReclamation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_rec = $_POST['id_reclamation'];
            $nouveau_msg = $_POST['nouveau_message'];
            $date = date('d/m H:i');

            // On récupère l'ancienne description pour ajouter la suite
            $stmt = $this->pdo->prepare("SELECT description FROM reclamation WHERE id_reclamation = ?");
            $stmt->execute([$id_rec]);
            $old_desc = $stmt->fetchColumn();

            $update_desc = $old_desc . "\n\n[CLIENT le $date] : " . $nouveau_msg;

            // On met à jour et on repasse le statut en 'En attente' pour que l'employé le voit
            $sql = "UPDATE reclamation SET description = ?, statut = 'En attente' WHERE id_reclamation = ?";
            $this->pdo->prepare($sql)->execute([$update_desc, $id_rec]);

            header("Location: index.php?page=mes_reclamations&success=Message envoyé");
            exit();
        }
    }

    // C:\laragon\www\app_pressing\app\controllers\ClientController.php

    public function creerReclamation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Récupération des données du formulaire
            $id_commande = $_POST['id_commande'];
            $description = trim($_POST['description']);
            $type = $_POST['type_reclamation'] ?? 'commande_non_conforme';
            
            // 2. Récupération de l'ID du client connecté depuis la session
            $id_client = $_SESSION['id_utilisateur']; 

            try {
                // 3. La requête inclut maintenant id_client
                $sql = "INSERT INTO reclamation (id_reclamation, id_client, type_reclamation, description, statut, id_commande) 
                        VALUES (NULL, ?, ?, ?, 'En attente', ?)";
                
                $stmt = $this->pdo->prepare($sql);
                
                // 4. On passe les variables dans l'ordre des (?)
                $stmt->execute([
                    $id_client, 
                    $type, 
                    $description, 
                    $id_commande
                ]);

                // Redirection vers la liste des réclamations avec un message de succès
                header("Location: index.php?page=mes_reclamations&msg=success");
                exit();

            } catch (PDOException $e) {
                // En cas d'erreur, on l'affiche pour déboguer
                die("Erreur lors de la création de la réclamation : " . $e->getMessage());
            }
        }
    }
}