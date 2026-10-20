<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class EmployeController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * SÉCURITÉ : Vérifie si l'employé est connecté
     */
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['employe_id'])) {
            header("Location: index.php?page=login");
            exit;
        }
    }

    // Dans app/models/Employe.php

    public function login(string $email, string $password) {
        // On vérifie l'email, le mot de passe, le rôle 'employe' et le statut 'actif'
        $sql = "SELECT u.*, e.id_employe, e.poste 
                FROM utilisateur u 
                JOIN employe e ON u.id_utilisateur = e.id_utilisateur 
                WHERE u.email = :email 
                AND u.role = 'employe' 
                AND u.statut = 'actif' 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comparaison directe (non hachée comme demandé)
        if ($user && $password === $user['mot_de_passe']) {
            return $user;
        }
        return null;
    }

    // --- 1. DASHBOARD & GESTION DES PRIORITÉS ---
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employe') {
            header("Location: index.php?page=choix_role");
            exit();
        }

        // --- 1. INITIALISATION DES VARIABLES (Évite les Warnings) ---
        $stats = [
            'commandes_jour'     => 0, 
            'factures_impayer'   => 0, 
            'ca_du_jour'         => 0, 
            'livraisons_attente' => 0, 
            'reclamations_neuves'=> 0,
            'prioritaires'       => 0  
        ];

        $commandes_actives = []; 
        $factures_ouvertes = [];
        $reclamations      = [];
        $livreurs          = [];

        // --- 2. RÉCUPÉRATION DES DONNÉES ---
        try {
            // Chiffre d'affaires du jour (Somme des paiements réels)
            $sqlCA = "SELECT SUM(montant) FROM paiement WHERE DATE(date_paiement) = CURDATE()";
            $stats['ca_du_jour'] = (float)($this->pdo->query($sqlCA)->fetchColumn() ?: 0);

            // Factures impayées
            $sqlImp = "SELECT COUNT(*) FROM facture WHERE statut = 'non payée'";
            $stats['factures_impayer'] = (int)$this->pdo->query($sqlImp)->fetchColumn();

            // Commandes du jour
            $sqlCmdJour = "SELECT COUNT(*) FROM commande WHERE DATE(date_commande) = CURDATE()";
            $stats['commandes_jour'] = (int)$this->pdo->query($sqlCmdJour)->fetchColumn();

            // Livraisons en attente (Table livraison)
            $sqlLivAtt = "SELECT COUNT(*) FROM livraison WHERE statut = 'en attente' OR statut = 'en cours'";
            $stats['livraisons_attente'] = (int)$this->pdo->query($sqlLivAtt)->fetchColumn();

            // Réclamations non traitées
            $sqlRecNum = "SELECT COUNT(*) FROM reclamation WHERE statut != 'traité'";
            $stats['reclamations_neuves'] = (int)$this->pdo->query($sqlRecNum)->fetchColumn();

            // --- LISTE DES COMMANDES (Filtre Urgence + Date) ---
            // Note : On utilise JOIN client + utilisateur pour avoir le nom
            $sqlTableau = "SELECT c.*, u.nom, u.prenom, cl.telephone 
                        FROM commande c 
                        JOIN client cl ON c.id_client = cl.id_client
                        JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur 
                        WHERE c.statut != 'livré' 
                        ORDER BY (c.statut = 'Express') DESC, c.date_commande ASC LIMIT 15";
            $commandes_actives = $this->pdo->query($sqlTableau)->fetchAll(PDO::FETCH_ASSOC);

            // --- FACTURES OUVERTES (Pour l'encaissement rapide) ---
            $sqlFact = "SELECT f.*, u.nom, u.prenom 
                        FROM facture f
                        JOIN commande c ON f.id_commande = c.id_commande
                        JOIN client cl ON c.id_client = cl.id_client
                        JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                        WHERE f.statut = 'non payée' ORDER BY f.date_facture DESC LIMIT 5";
            $factures_ouvertes = $this->pdo->query($sqlFact)->fetchAll(PDO::FETCH_ASSOC);

            // --- RÉCLAMATIONS ---
            $sqlRec = "SELECT r.*, u.nom 
                    FROM reclamation r
                    JOIN commande c ON r.id_commande = c.id_commande
                    JOIN client cl ON c.id_client = cl.id_client
                    JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
                    WHERE r.statut != 'traité' ORDER BY r.id_reclamation DESC";
            $reclamations = $this->pdo->query($sqlRec)->fetchAll(PDO::FETCH_ASSOC);

            // --- LISTE DES LIVREURS (Pour l'assignation) ---
            $sqlLiv = "SELECT l.id_livreur, u.nom 
                    FROM livreur l 
                    JOIN utilisateur u ON l.id_utilisateur = u.id_utilisateur";
            $livreurs = $this->pdo->query($sqlLiv)->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            // Log d'erreur optionnel : error_log($e->getMessage());
        }

        include '../app/views/employe/dashboard.php';
    }


    // --- 2. CRÉATION DE COMMANDE & CALCUL DE PRIX ---

    public function enregistrerArticle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_commande = $_POST['id_commande'];
            $id_article = $_POST['id_article'];
            $id_soin = $_POST['id_soin'];
            $quantite = $_POST['quantite'];

            // Calcul automatique du prix unitaire (Prix Base + Prix Soin)
            $stmt = $this->pdo->prepare("
                SELECT (a.prix_base_nettoyage + s.prix_soin) as total 
                FROM catalogue_articles a, options_soin s 
                WHERE a.id_article = ? AND s.id_soin = ?
            ");
            $stmt->execute([$id_article, $id_soin]);
            $prix_unitaire = $stmt->fetchColumn();

            // Insertion dans details_commande
            $ins = $this->pdo->prepare("
                INSERT INTO details_commande (id_commande, id_article, id_soin, quantite, prix_unitaire_applique) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $ins->execute([$id_commande, $id_article, $id_soin, $quantite, $prix_unitaire]);
            
            header("Location: index.php?page=detail_commande&id=$id_commande");
        }
    }

    // --- 3. FACTURATION & ENCAISSEMENT (LOGIQUE PRO) ---
    public function validerCaisse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_commande = $_POST['id_commande'];
            $montant_total = $_POST['montant_total'];
            $montant_verse = $_POST['montant_verse'];
            $mode = $_POST['mode_paiement']; // especes, carte, mobile_money

            try {
                $this->pdo->beginTransaction();

                // 1. Générer la Facture
                $stmtF = $this->pdo->prepare("INSERT INTO facture (date_facture, montant_total, id_commande, statut, mode_paiement) VALUES (CURDATE(), ?, ?, 'non payée', ?)");
                $stmtF->execute([$montant_total, $id_commande, $mode]);
                $id_facture = $this->pdo->lastInsertId();

                // 2. Enregistrer le Paiement
                $stmtP = $this->pdo->prepare("INSERT INTO paiement (date_paiement, montant, mode_paiement, id_commande) VALUES (CURDATE(), ?, ?, ?)");
                $stmtP->execute([$montant_verse, $mode, $id_commande]);

                // 3. Update Statut Facture si payée en totalité
                if ($montant_verse >= $montant_total) {
                    $this->pdo->prepare("UPDATE facture SET statut = 'payée' WHERE id_commande = ?")->execute([$id_commande]);
                }

                $this->pdo->commit();
                header("Location: index.php?page=recap_paiement&id=$id_commande");
            } catch (Exception $e) {
                $this->pdo->rollBack();
                die("Erreur de caisse : " . $e->getMessage());
            }
        }
    }

    // --- 4. LOGISTIQUE & LIVRAISON ---
    public function assignerLivreur() {
        $this->checkAuth();
        $id_commande = $_GET['id'];
        
        // Voir tous les livreurs disponibles
        $livreurs = $this->pdo->query("SELECT l.id_livreur, u.nom, u.prenom FROM livreur l JOIN utilisateur u ON l.id_utilisateur = u.id_utilisateur")->fetchAll();
        
        // Récupérer l'adresse du client automatiquement
        $stmt = $this->pdo->prepare("SELECT cl.adresse FROM client cl JOIN commande c ON cl.id_client = c.id_client WHERE c.id_commande = ?");
        $stmt->execute([$id_commande]);
        $adresse_client = $stmt->fetchColumn();

        include '../app/views/employe/assign_livraison.php';
    }

    // --- 5. RÉCLAMATIONS & INCIDENTS ---
    public function gestionIncidents() {
        $this->checkAuth();
        $query = "SELECT r.*, u.nom, c.id_commande 
                  FROM reclamation r 
                  JOIN commande c ON r.id_commande = c.id_commande 
                  JOIN client cl ON c.id_client = cl.id_client
                  JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur";
        $reclamations = $this->pdo->query($query)->fetchAll();
        include '../app/views/employe/reclamations.php';
    }

    // --- 6. PROFIL & SETTINGS ---
    public function settings() {
        $this->checkAuth();
        $id_u = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT u.*, e.poste FROM utilisateur u JOIN employe e ON u.id_utilisateur = e.id_utilisateur WHERE u.id_utilisateur = ?");
        $stmt->execute([$id_u]);
        $employe = $stmt->fetch();
        include '../app/views/employe/settings.php';
    }

    public function get_commande_full_details() {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            // On joint les tables pour avoir les noms et les prix réels
            $sql = "SELECT 
                        dc.quantite,
                        ca.nom_article, 
                        ca.prix_base_nettoyage,
                        os.nom_soin,
                        os.prix_soin
                    FROM details_commande dc
                    INNER JOIN catalogue_articles ca ON dc.id_article = ca.id_article
                    LEFT JOIN options_soin os ON dc.id_soin = os.id_soin
                    WHERE dc.id_commande = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['items' => $items]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['items' => [], 'error' => $e->getMessage()]);
        }
        exit();
    }

    public function lancerLivraison() {
        // 1. On vérifie que les données existent avant de les utiliser
        $id_commande = $_POST['id_commande'] ?? null;
        $id_livreur = $_POST['id_livreur'] ?? null;
        $adresse = $_POST['adresse_livraison'] ?? 'Non spécifiée';

        // 2. Si on n'a pas de livreur ou de commande, on ne fait rien (on évite le Fatal Error)
        if (!$id_commande || !$id_livreur) {
            header("Location: index.php?page=employe_dashboard&error=missing_data");
            exit();
        }

        try {
            // Ta requête SQL ici...
            $sql = "INSERT INTO livraison (id_commande, id_livreur, adresse_livraison, statut_livraison) 
                    VALUES (?, ?, ?, 'En cours')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_commande, $id_livreur, $adresse]);

            // Mise à jour du statut de la commande
            $this->pdo->prepare("UPDATE commande SET statut = 'En cours de livraison' WHERE id_commande = ?")
                    ->execute([$id_commande]);

            header("Location: index.php?page=employe_dashboard&success=expedie");
        } catch (PDOException $e) {
            die("Erreur base de données : " . $e->getMessage());
        }
    }

    public function afficherFormulaireDepot() {
        // Vérifie si la session contient bien le rôle employé
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employe') {
            header('Location: index.php?page=choix_role');
            exit();
        }

        // Récupération des données
        $clients = $this->pdo->query("SELECT cl.id_client, u.nom, u.prenom FROM client cl JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur ORDER BY u.nom ASC")->fetchAll(PDO::FETCH_ASSOC);
        $articles = $this->pdo->query("SELECT * FROM catalogue_articles")->fetchAll(PDO::FETCH_ASSOC);
        $soins = $this->pdo->query("SELECT * FROM options_soin")->fetchAll(PDO::FETCH_ASSOC);

        // Inclusion du fichier avec ton nom spécifique
        include '../app/views/employe/nouvelle_commades.php'; 
    }

    public function enregistrer_depot() {
        try {
            // On vérifie que les données essentielles sont présentes
            if (!isset($_POST['id_client']) || empty($_POST['articles'])) {
                throw new Exception("Données du formulaire incomplètes.");
            }

            $this->pdo->beginTransaction();

            // 1. Insertion de la commande principale
            // On récupère le statut (Normal ou Express) depuis le formulaire
            $statut_initial = $_POST['statut'] ?? 'En attente';
            
            $stmt = $this->pdo->prepare("INSERT INTO commande (id_client, date_commande, statut, notes) VALUES (?, NOW(), ?, ?)");
            $stmt->execute([
                $_POST['id_client'],
                $statut_initial,
                $_POST['notes'] ?? ''
            ]);
            
            $id_commande = $this->pdo->lastInsertId();

            // 2. Insertion des détails de la commande
            $stmtArt = $this->pdo->prepare("INSERT INTO details_commande (id_commande, id_article, id_soin, quantite) VALUES (?, ?, ?, ?)");
            
            // Comme ton formulaire envoie articles[0][id_article], articles[1][id_article], etc.
            // On boucle sur le tableau 'articles'
            foreach ($_POST['articles'] as $articleData) {
                // Sécurité : on vérifie que l'ID de l'article est présent
                if (!empty($articleData['id_article'])) {
                    $stmtArt->execute([
                        $id_commande,
                        $articleData['id_article'],
                        $articleData['id_soin'] ?? null,
                        $articleData['quantite'] ?? 1
                    ]);
                }
            }

            $this->pdo->commit();
            
            // Redirection vers le dashboard avec un message de succès
            header("Location: index.php?page=dashboard&msg=success_depot");
            exit();

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // Affichage de l'erreur pour le débogage
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    public function update_statut() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_commande = $_POST['id_commande'];
            $nouveau_statut = $_POST['statut']; // On garde la valeur telle quelle (ex: "Prêt")

            try {
                $stmt = $this->pdo->prepare("UPDATE commande SET statut = ? WHERE id_commande = ?");
                // Ligne 372
                $stmt->execute([$nouveau_statut, $id_commande]);
                
                header("Location: index.php?page=dashboard_employe");
            } catch (PDOException $e) {
                die("Erreur : " . $e->getMessage());
            }
        }
    }

    
// ... haut du fichier ...
    public function afficherFacture() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: index.php?page=dashboard"); exit; }

        try {
            // 1. Récupérer les infos de la commande et l'identité du client via la table utilisateur
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.nom, u.prenom, cl.telephone 
                FROM commande c 
                JOIN client cl ON c.id_client = cl.id_client 
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur 
                WHERE c.id_commande = ?
            ");
            $stmt->execute([$id]);
            $commande = $stmt->fetch();

            if (!$commande) { die("Commande introuvable."); }

            // 2. Récupérer les articles avec le nom exact : prix_base_nettoyage
            $stmtItems = $this->pdo->prepare("
                SELECT dc.*, ca.nom_article, os.nom_soin, ca.prix_base_nettoyage, os.prix_soin 
                FROM details_commande dc
                JOIN catalogue_articles ca ON dc.id_article = ca.id_article
                JOIN options_soin os ON dc.id_soin = os.id_soin
                WHERE dc.id_commande = ?
            ");
            $stmtItems->execute([$id]);
            $items = $stmtItems->fetchAll();

            include '../app/views/employe/facturation.php';

        } catch (PDOException $e) {
            die("Erreur SQL : " . $e->getMessage());
        }
    }

    public function encaisserCommande() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_commande = $_POST['id_commande'];
            $montant = $_POST['montant'];
            
            // On adapte la méthode pour qu'elle corresponde aux valeurs de ton ENUM
            $methode_post = $_POST['methode']; 
            $methode_enum = 'especes'; // Valeur par défaut
            
            if ($methode_post === 'Mobile Money') $methode_enum = 'mobile_money';
            if ($methode_post === 'Carte') $methode_enum = 'carte';

            try {
                $this->pdo->beginTransaction();

                // 1. Mise à jour de la table commande (Assure-toi d'avoir fait le ALTER TABLE avant)
                $stmt = $this->pdo->prepare("UPDATE commande SET paiement_statut = 'Payé', methode_paiement = ? WHERE id_commande = ?");
                $stmt->execute([$methode_post, $id_commande]);

                // 2. Enregistrement dans la table paiement (On utilise mode_paiement ici)
                $stmtPay = $this->pdo->prepare("INSERT INTO paiement (id_commande, montant, mode_paiement, date_paiement) VALUES (?, ?, ?, NOW())");
                $stmtPay->execute([$id_commande, $montant, $methode_enum]);

                $this->pdo->commit();
                header("Location: index.php?page=dashboard&msg=success");
                exit;

            } catch (Exception $e) {
                $this->pdo->rollBack();
                die("Erreur lors de l'encaissement : " . $e->getMessage());
            }
        }
    }

    public function afficherCaisse() {
        // On récupère les paiements de la journée (CURDATE)
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nom, u.prenom 
            FROM paiement p
            JOIN commande c ON p.id_commande = c.id_commande
            JOIN client cl ON c.id_client = cl.id_client
            JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
            WHERE DATE(p.date_paiement) = CURDATE()
            ORDER BY p.date_paiement DESC
        ");
        $stmt->execute();
        $paiements = $stmt->fetchAll();

        // On prépare les totaux pour les petits blocs en haut de page
        $stmtTotaux = $this->pdo->prepare("
            SELECT mode_paiement, SUM(montant) as total 
            FROM paiement 
            WHERE DATE(date_paiement) = CURDATE() 
            GROUP BY mode_paiement
        ");
        $stmtTotaux->execute();
        $totaux = $stmtTotaux->fetchAll(PDO::FETCH_KEY_PAIR);

        // On envoie toutes ces données à la vue
        include '../app/views/employe/caisse.php';
    }

    public function preparerLivraison() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: index.php?page=dashboard"); exit; }

        try {
            // 1. Récupérer les infos commande + adresse client
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.nom, u.prenom, cl.telephone, cl.adresse 
                FROM commande c 
                JOIN client cl ON c.id_client = cl.id_client 
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur 
                WHERE c.id_commande = ?
            ");
            $stmt->execute([$id]);
            $commande = $stmt->fetch();

            // 2. CORRECTION : Récupérer les vrais IDs de la table 'livreur'
            // On joint la table livreur avec utilisateur pour avoir le nom
            $stmtL = $this->pdo->prepare("
                SELECT l.id_livreur, u.nom 
                FROM livreur l
                JOIN utilisateur u ON l.id_utilisateur = u.id_utilisateur
            ");
            $stmtL->execute();
            $livreurs = $stmtL->fetchAll();

            include '../app/views/employe/livraison_form.php';
        } catch (PDOException $e) {
            die("Erreur SQL : " . $e->getMessage());
        }
    }

    public function validerLivraison() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cmd = $_POST['id_commande'];
            $id_liv = $_POST['id_livreur'];
            $adr = $_POST['adresse_livraison'];

            try {
                $this->pdo->beginTransaction();

                // Insertion selon TA table (id_commande, id_livreur, adresse_livraison, date_livraison, statut)
                $stmt = $this->pdo->prepare("
                    INSERT INTO livraison (id_commande, id_livreur, adresse_livraison, date_livraison, statut) 
                    VALUES (?, ?, ?, CURDATE(), 'En cours')
                    ON DUPLICATE KEY UPDATE id_livreur = ?, adresse_livraison = ?, statut = 'En cours'
                ");
                $stmt->execute([$id_cmd, $id_liv, $adr, $id_liv, $adr]);

                // Mise à jour du statut global de la commande
                $stmtUpd = $this->pdo->prepare("UPDATE commande SET statut = 'En cours de livraison' WHERE id_commande = ?");
                $stmtUpd->execute([$id_cmd]);

                $this->pdo->commit();
                header("Location: index.php?page=dashboard&msg=success_livraison");
                exit;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                die("Erreur technique : " . $e->getMessage());
            }
        }
    }

    public function afficherLogistique() {
        try {
            // On récupère les livraisons, le nom du client et les infos commande
            $stmt = $this->pdo->prepare("
                SELECT l.*, c.date_commande, u_cli.nom as client_nom, u_cli.prenom as client_prenom, 
                    u_liv.nom as livreur_nom
                FROM livraison l
                JOIN commande c ON l.id_commande = c.id_commande
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u_cli ON cl.id_utilisateur = u_cli.id_utilisateur
                JOIN livreur liv ON l.id_livreur = liv.id_livreur
                JOIN utilisateur u_liv ON liv.id_utilisateur = u_liv.id_utilisateur
                ORDER BY l.date_livraison DESC
            ");
            $stmt->execute();
            $livraisons = $stmt->fetchAll();

            include '../app/views/employe/logistique.php';
        } catch (PDOException $e) {
            die("Erreur Logistique : " . $e->getMessage());
        }
    }

    // Affichage principal : Liste + Chat
    public function afficherIncidents() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // On trie par id_reclamation car date_creation n'existe pas dans ta table
        $stmt = $this->pdo->query("SELECT r.*, u.nom, u.prenom FROM reclamation r 
            JOIN commande c ON r.id_commande = c.id_commande
            JOIN client cl ON c.id_client = cl.id_client
            JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur
            ORDER BY r.id_reclamation DESC"); 
        $incidents = $stmt->fetchAll();

        $ticket_actuel = null;
        $messages = [];

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->pdo->prepare("SELECT r.*, u.nom, u.prenom FROM reclamation r 
                JOIN commande c ON r.id_commande = c.id_commande
                JOIN client cl ON c.id_client = cl.id_client
                JOIN utilisateur u ON cl.id_utilisateur = u.id_utilisateur WHERE r.id_reclamation = ?");
            $stmt->execute([$id]);
            $ticket_actuel = $stmt->fetch();

            // On garde le tri par date_envoi pour les messages car cette table possède une date
            $stmt = $this->pdo->prepare("SELECT m.*, u.role FROM reclamation_messages m 
                JOIN utilisateur u ON m.id_emetteur = u.id_utilisateur 
                WHERE m.id_reclamation = ? ORDER BY m.date_envoi ASC");
            $stmt->execute([$id]);
            $messages = $stmt->fetchAll();
        }

        include '../app/views/employe/incidents.php';
    }
    // Répondre à un ticket
    public function repondreIncident() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_rec = $_POST['id_reclamation'] ?? null;
            $msg = trim($_POST['message'] ?? '');
            $id_me = $_SESSION['id_utilisateur'] ?? $_SESSION['id_employe'] ?? null;

            if ($id_rec && $id_me && !empty($msg)) {
                try {
                    // --- C'EST ICI QUE TU METS LE CODE ---
                    $stmt = $this->pdo->prepare("INSERT INTO reclamation_messages (id_reclamation, id_emetteur, message, role) VALUES (?, ?, ?, 'employe')");
                    $stmt->execute([$id_rec, $id_me, $msg]);
                    // --------------------------------------

                    // On met à jour le statut du ticket pour le passer "en cours"
                    $this->pdo->prepare("UPDATE reclamation SET statut = 'en_cours' WHERE id_reclamation = ?")
                            ->execute([$id_rec]);

                    header("Location: index.php?page=reclamations&id=" . $id_rec);
                    exit();
                } catch (Exception $e) {
                    die("Erreur base de données : " . $e->getMessage());
                }
            } else {
                die("Erreur : Données manquantes.");
            }
        }
    }

    // Clôture du ticket
    public function cloturerIncident() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("UPDATE reclamation SET statut = 'resolu' WHERE id_reclamation = ?");
            $stmt->execute([$id]);
            header("Location: index.php?page=reclamations&id=" . $id);
        } else {
            header("Location: index.php?page=reclamations");
        }
        exit();
    }

    public function gererProfil() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Correction : Utiliser l'id_utilisateur (clé étrangère commune)
        $id_me = $_SESSION['id_utilisateur'] ?? null;
        
        if (!$id_me) {
            header("Location: index.php?page=login");
            exit();
        }

        $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $password = $_POST['nouveau_password'];

            // Mise à jour Nom et Prénom
            $stmt = $this->pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ? WHERE id_utilisateur = ?");
            $stmt->execute([$nom, $prenom, $id_me]);

            // Mise à jour Mot de Passe
            if (!empty($password)) {
                $stmt = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
                $stmt->execute([$password, $id_me]);
            }
            
            $success = "Profil mis à jour.";
            $_SESSION['nom'] = $nom; 
            
            // On recharge les données pour la vue
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
            $stmt->execute([$id_me]);
            $user = $stmt->fetch();
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
            $stmt->execute([$id_me]);
            $user = $stmt->fetch();
        }

        // Sécurité : Si l'utilisateur n'est pas trouvé, on crée un tableau vide pour éviter les erreurs de vue
        if (!$user) {
            $user = ['nom' => '', 'prenom' => '', 'email' => ''];
        }

        include '../app/views/employe/profil.php';
    }
}