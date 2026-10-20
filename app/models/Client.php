<?php

class Client {
    private $db;

    public function __construct($pdo){
        $this->db = $pdo;
    }

    /**
     * 🔐 Vérifier si un client existe via son email
     * Utilisé pour la connexion client
     */
   public function getClientByEmail($email){
    $stmt = $this->db->prepare("
        SELECT u.*, c.id_client
        FROM utilisateur u
        JOIN client c ON u.id_utilisateur = c.id_utilisateur
        WHERE u.email = ? AND u.role = 'client'
    ");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    /**
     * 📄 Récupérer un client via l'id utilisateur
     */
    public function getClientByUserId($id_utilisateur){
        $stmt = $this->db->prepare("
            SELECT * FROM client 
            WHERE id_utilisateur = ?
        ");
        $stmt->execute([$id_utilisateur]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 📦 Commandes du client
     */
    public function getCommandes($id_client){
        $stmt = $this->db->prepare("
            SELECT * FROM commande
            WHERE id_client = ?
            ORDER BY date_commande DESC
        ");
        $stmt->execute([$id_client]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * 🧾 Factures du client
     */

    // ... (gardez vos méthodes existantes getClientByEmail)

    // NOUVEAUTÉ : Récupérer les factures du client
    public function getFactures($id_client){
        $stmt = $this->db->prepare("SELECT f.* FROM facture f JOIN commande c ON f.id_commande = c.id_commande WHERE c.id_client = ?");
        $stmt->execute([$id_client]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NOUVEAUTÉ : Récupérer la toute dernière commande pour le tracker de luxe
    public function getLastOrder($id_client){
        $stmt = $this->db->prepare("SELECT * FROM commande WHERE id_client = ? ORDER BY date_commande DESC LIMIT 1");
        $stmt->execute([$id_client]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * ✏️ Mettre à jour le profil client
     */
        public function updateProfile($id_utilisateur, $nom, $prenom, $email) {
          $stmt = $this->db->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?");
           return $stmt->execute([$nom, $prenom, $email, $id_utilisateur]);
        }

        public function updatePassword($id_utilisateur, $new_password) {
 // Note : Ici on reste en clair comme demandé, mais prévoyez password_hash plus tard
         $stmt = $this->db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
         return $stmt->execute([$new_password, $id_utilisateur]);
        }

    public function createCommande($id_client, $vêtement, $service, $notes, $date_retrait) {
        try {
            $this->db->beginTransaction();

            // 1. Insertion dans la table 'commande'
            // Ta table a : id_commande, date_commande, statut, id_client, id_employe
            $sqlCmd = "INSERT INTO commande (id_client, date_commande, statut) VALUES (?, NOW(), 'en attente')";
            $stmtCmd = $this->db->prepare($sqlCmd);
            $stmtCmd->execute([$id_client]);
            
            $id_commande = $this->db->lastInsertId();

            // 2. Insertion dans la table 'vetement' (pour lier le type de vêtement à la commande)
            // Ta table a : id_vetement, type, couleur, tissu, id_commande, id_service
            // On met les 'notes' dans le champ 'tissu' par défaut si tu n'as pas de champ notes
            $sqlVet = "INSERT INTO vetement (type, id_commande, tissu) VALUES (?, ?, ?)";
            $stmtVet = $this->db->prepare($sqlVet);
            $stmtVet->execute([$vêtement, $id_commande, $notes]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage()); // Pour voir l'erreur dans les logs de Laragon
            return false;
        }
    }

        // Récupérer l'historique avec les paiements
    public function getFullHistory($id_client) {
        $sql = "SELECT c.*, p.montant, p.date_paiement, p.mode_paiement 
                FROM commande c 
                LEFT JOIN paiement p ON c.id_commande = p.id_commande 
                WHERE c.id_client = ? 
                ORDER BY c.date_commande DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_client]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les détails d'une commande (vêtements et services)
    public function getOrderDetails($id_commande) {
        $sql = "SELECT v.*, s.nom_service, s.prix_unitaire 
                FROM vetement v
                LEFT JOIN service s ON v.id_service = s.id_service
                WHERE v.id_commande = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_commande]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Enregistrer une réclamation
    public function saveReclamation($id_commande, $type, $description) {
        $sql = "INSERT INTO reclamation (id_commande, type_reclamation, description, statut) 
                VALUES (?, ?, ?, 'En attente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_commande, $type, $description]);
    }

    public function createFullCommande($id_client, $articles, $soins, $qtys) {
        try {
            $this->db->beginTransaction();

            // 1. On crée la commande principale
            $sql = "INSERT INTO commande (id_client, date_commande, statut) VALUES (?, NOW(), 'en attente')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_client]);
            $id_commande = $this->db->lastInsertId();

            // 2. On prépare l'insertion des détails (Vêtements + Soins)
            $sqlDetail = "INSERT INTO details_commande (id_commande, id_article, id_soin, quantite) 
                        VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);

            // 3. On boucle sur les tableaux envoyés par le formulaire
            foreach ($articles as $index => $id_article) {
                $stmtDetail->execute([
                    $id_commande, 
                    $id_article, 
                    $soins[$index], 
                    $qtys[$index]
                ]);
            }

            $this->db->commit();
            return $id_commande;
        } catch (Exception $e) {
            $this->db->rollBack();
            die("Erreur lors du dépôt : " . $e->getMessage());
         }
    }


}

