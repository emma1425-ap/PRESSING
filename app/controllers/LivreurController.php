<?php

class LivreurController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function checkAccess() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'livreur') {
            header("Location: index.php?page=livreur_login");
            exit();
        }
        return $_SESSION['id_utilisateur'];
    }

    public function dashboard() {
        $id_u = $this->checkAccess();

        // On cible uniquement la table livraison et on remonte vers l'utilisateur (client)
        $sql = "SELECT 
                    liv.id_livraison, liv.adresse_livraison, liv.statut, liv.date_livraison,
                    c.id_commande,
                    u.nom AS client_nom, u.prenom AS client_prenom
                FROM livraison liv
                JOIN commande c ON liv.id_commande = c.id_commande
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                JOIN livreur l ON liv.id_livreur = l.id_livreur
                WHERE l.id_utilisateur = ? AND liv.statut != 'Livrée'
                ORDER BY liv.date_livraison ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_u]);
        $missions = $stmt->fetchAll();

        include '../app/views/livreur/dashboard.php';
    }

    public function updateStatutLivraison() {
        $this->checkAccess();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_liv = $_POST['id_livraison'];
            $statut = $_POST['statut'];
            $id_cmd = $_POST['id_commande'];

            if ($statut === 'Problème') {
                $desc = $_POST['description_probleme'] ?? 'Problème de livraison signalé par le livreur';
                
                // 1. Marquer la livraison en "Problème"
                $stmt = $this->pdo->prepare("UPDATE livraison SET statut = 'Problème' WHERE id_livraison = ?");
                $stmt->execute([$id_liv]);

                // 2. Insérer la réclamation avec les colonnes exactes de ton schéma
                // On utilise 'commande_non_conforme' comme type par défaut pour le livreur
                $sqlRec = "INSERT INTO reclamation (type_reclamation, description, statut, id_commande) 
                        VALUES ('commande_non_conforme', ?, 'En attente', ?)";
                
                $this->pdo->prepare($sqlRec)->execute([
                    "LIVREUR : " . $desc, // On précise que ça vient du livreur dans le texte
                    $id_cmd
                ]);

            } else {
                // Mise à jour classique (En cours ou Livrée)
                $stmt = $this->pdo->prepare("UPDATE livraison SET statut = ? WHERE id_livraison = ?");
                $stmt->execute([$statut, $id_liv]);

                // Si Livrée, on synchronise la table commande
                if ($statut === 'Livrée') {
                    $stmtCmd = $this->pdo->prepare("UPDATE commande SET statut = 'Livrée' WHERE id_commande = ?");
                    $stmtCmd->execute([$id_cmd]);
                }
            }

            header("Location: index.php?page=livreur_dashboard&success=1");
            exit();
        }
    }

    public function gererProfil() {
        $id_u = $this->checkAccess(); 

        $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // On récupère uniquement ce qui est modifiable
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $password = $_POST['nouveau_password'] ?? '';

            // 1. Mise à jour uniquement du Nom et du Prénom (L'email reste intact)
            $stmt = $this->pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ? WHERE id_utilisateur = ?");
            $stmt->execute([$nom, $prenom, $id_u]);

            // 2. Mise à jour du Mot de Passe (si l'utilisateur en a saisi un nouveau)
            if (!empty($password)) {
                $stmt = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
                $stmt->execute([$password, $id_u]);
            }
            
            $success = "Votre profil d'ambassadeur a été mis à jour avec succès.";
            $_SESSION['nom'] = $nom; 
        }

        // On recharge les données pour l'affichage (ici on récupère bien l'email pour le montrer)
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_u]);
        $user = $stmt->fetch();

        include '../app/views/livreur/profil.php';
    }

    public function repondreReclamationLivreur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_rec = $_POST['id_reclamation'];
            $msg = trim($_POST['message']);
            $id_livreur = $_SESSION['id_utilisateur'];

            if (!empty($msg)) {
                // Forcez le rôle en minuscule et vérifiez qu'il correspond à votre ENUM SQL
                $stmt = $this->pdo->prepare("INSERT INTO reclamation_messages (id_reclamation, id_emetteur, message, role, date_envoi) VALUES (?, ?, ?, 'livreur', NOW())");
                $stmt->execute([$id_rec, $id_livreur, $msg]);
            }
        }
        header("Location: index.php?page=messages_livreur");
    }

    public function afficherMessagesLivreur() {
        // 1. Vérifier que le livreur est connecté
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id_livreur = $_SESSION['id_utilisateur'] ?? null;

        if (!$id_livreur) {
            header("Location: index.php?page=connexion");
            exit();
        }

        // 2. Récupérer les incidents signalés par CE livreur
        // On cherche les réclamations liées aux commandes dont il est le livreur
        // OU les réclamations qu'il a lui-même créées (selon votre BDD)
        $stmt = $this->pdo->prepare("SELECT * FROM reclamation WHERE description LIKE 'LIVREUR%' ORDER BY id_reclamation DESC");
        $stmt->execute();
        $mes_incidents = $stmt->fetchAll();

        // 3. Récupérer TOUS les messages du chat pour ces incidents
        $stmt_msg = $this->pdo->prepare("SELECT * FROM reclamation_messages ORDER BY date_envoi ASC");
        $stmt_msg->execute();
        $messages_internes = $stmt_msg->fetchAll();

        // 4. Charger la liste des commandes au cas où il veut signaler un nouveau souci
        $stmt_cmd = $this->pdo->prepare("SELECT * FROM commande WHERE id_livreur = ?");
        $stmt_cmd->execute([$id_livreur]);
        $mes_commandes = $stmt_cmd->fetchAll();

        // 5. Envoyer les données à la vue
        require_once '../app/views/livreur/messages_livreur.php';
    }
}