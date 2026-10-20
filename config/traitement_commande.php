<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 2. Vérifier si les données sont bien envoyées
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['articles'])) {
    
    // On récupère le bon ID client depuis la session
    $id_client = $_SESSION['client']['id_client'] ?? null;
    
    if (!$id_client) {
        header("Location: index.php?page=client_login");
        exit();
    }
    
    $articles = $_POST['articles']; 
    $soins    = $_POST['soins'];    
    $qtys     = $_POST['qtys'];     

    try {
        $pdo->beginTransaction();

        // ÉTAPE A : Créer la commande
        $sqlCmd = "INSERT INTO commande (id_client, date_commande, statut) VALUES (?, NOW(), 'En attente')";
        $stmtCmd = $pdo->prepare($sqlCmd);
        $stmtCmd->execute([$id_client]);
        
        $id_commande = $pdo->lastInsertId();

        // ÉTAPE B : Insérer les vêtements (details_commande)
        $sqlDetail = "INSERT INTO details_commande (id_commande, id_article, id_soin, quantite) VALUES (?, ?, ?, ?)";
        $stmtDetail = $pdo->prepare($sqlDetail);

        foreach ($articles as $index => $id_article) {
            if (!empty($id_article)) {
                $stmtDetail->execute([
                    $id_commande,
                    $id_article,
                    $soins[$index],
                    $qtys[$index]
                ]);
            }
        }

        $pdo->commit();

        // ÉTAPE C : Redirection vers le Dashboard avec le message de succès
        header("Location: index.php?page=client_dashboard&success=order_ok");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
} else {
    header("Location: index.php?page=nouvelle_commande");
    exit();
}