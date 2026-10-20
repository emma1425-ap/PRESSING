<?php
session_start();
// Connexion BDD
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['client']['id_client'])) {
    $id_client = $_SESSION['client']['id_client'];
    $old_pw = $_POST['old_password'];
    $new_pw = $_POST['new_password'];

    // 1. Récupérer le mot de passe actuel en BDD
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM client WHERE id_client = ?");
    $stmt->execute([$id_client]);
    $user = $stmt->fetch();

    // 2. Vérifier si l'ancien mot de passe est correct
    // Note : Utilise password_verify() si tes mots de passe sont hachés (recommandé)
    if ($user && password_verify($old_pw, $user['mot_de_passe'])) {
        
        // 3. Hacher le nouveau mot de passe
        $new_pw_hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        
        // 4. Mettre à jour
        $update = $pdo->prepare("UPDATE client SET mot_de_passe = ? WHERE id_client = ?");
        $update->execute([$new_pw_hashed, $id_client]);

        // Redirection avec succès
        header("Location: index.php?page=setting&status=success");
        exit;
    } else {
        // Redirection avec erreur
        header("Location: index.php?page=setting&status=error_old_pw");
        exit;
    }
}
?>