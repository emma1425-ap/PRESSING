<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 2. On vérifie que le formulaire a bien été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération et protection des données
    $id_commande = $_POST['id_commande'] ?? null;
    $type_reclamation = $_POST['type_reclamation'] ?? null;
    $description = htmlspecialchars($_POST['description'] ?? '');
    $statut = 'En attente'; // Statut par défaut à la création

    // Vérification que les champs ne sont pas vides
    if (empty($id_commande) || empty($type_reclamation) || empty($description)) {
        die("Erreur : Veuillez remplir tous les champs du formulaire.");
    }

    try {
        // 3. Préparation de la requête SQL (Respect exact de tes colonnes)
        $sql = "INSERT INTO reclamation (type_reclamation, description, statut, id_commande) 
                VALUES (:type_rec, :descr, :stat, :id_cmd)";
        
        $stmt = $pdo->prepare($sql);
        
        // 4. Exécution
        $stmt->execute([
            ':type_rec' => $type_reclamation,
            ':descr'    => $description,
            ':stat'     => $statut,
            ':id_cmd'   => $id_commande
        ]);

        // 5. Redirection vers le dashboard avec un message de succès
        header("Location: index.php?page=client_dashboard&success=reclamation_sent");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur SQL (par exemple si l'ID commande n'existe pas)
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
} else {
    // Si on tente d'accéder au fichier sans passer par le formulaire
    header("Location: index.php?page=client_dashboard");
    exit();
}