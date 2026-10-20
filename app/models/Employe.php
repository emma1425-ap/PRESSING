<?php

class Employe {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // --- 1. AUTHENTIFICATION ---

    public function login(string $email, string $password) {
        $sql = "SELECT u.*, e.id_employe, e.poste, e.telephone as tel_pro 
                FROM utilisateur u 
                JOIN employe e ON u.id_utilisateur = e.id_utilisateur 
                WHERE u.email = :email LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['mot_de_passe']) {
            return $user;
        }
        return null;
    }

    // --- 2. STATISTIQUES (CARTES) ---

    public function countDailyOrders() {
        $sql = "SELECT COUNT(*) FROM commande WHERE date_commande = CURDATE()";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function countUnpaidInvoices() {
        $sql = "SELECT COUNT(*) FROM facture WHERE statut = 'non payée'";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function getDailyRevenue() {
        $sql = "SELECT SUM(montant) FROM paiement WHERE date_paiement = CURDATE()";
        $res = $this->pdo->query($sql)->fetchColumn();
        return $res ?: 0;
    }

    public function countPendingDeliveries() {
        $sql = "SELECT COUNT(*) FROM livraison WHERE statut = 'En attente'";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    // --- 3. LISTES DASHBOARD & CAISSE ---

    public function getDashboardOrders() {
        $sql = "SELECT c.*, u.nom, u.prenom, u.telephone 
                FROM commande c
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                WHERE c.statut != 'Livrée'
                ORDER BY (c.statut = 'Express') DESC, c.date_commande ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOpenInvoices() {
        $sql = "SELECT f.*, u.nom, u.prenom 
                FROM facture f
                JOIN commande c ON f.id_commande = c.id_commande
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                WHERE f.statut = 'non payée'
                ORDER BY f.date_facture ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 4. CALCULS & DÉTAILS ---

    public function calculateServicePrice(int $id_article, int $id_soin) {
        $sql = "SELECT (a.prix_base_nettoyage + s.prix_soin) as total 
                FROM catalogue_articles a, options_soin s 
                WHERE a.id_article = ? AND s.id_soin = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_article, $id_soin]);
        return $stmt->fetchColumn();
    }

    public function getTotalPaidAmount(int $id_commande) {
        $sql = "SELECT SUM(montant) FROM paiement WHERE id_commande = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_commande]);
        return $stmt->fetchColumn() ?: 0;
    }

    // --- 5. LOGISTIQUE & RÉCLAMATIONS ---

    public function getLivreursList() {
        $sql = "SELECT l.id_livreur, u.nom, u.prenom, l.telephone 
                FROM livreur l 
                JOIN utilisateur u ON l.id_utilisateur = u.id_utilisateur";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveReclamations() {
        $sql = "SELECT r.*, u.nom, u.prenom, c.date_commande 
                FROM reclamation r 
                JOIN commande c ON r.id_commande = c.id_commande
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                ORDER BY r.id_reclamation DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}